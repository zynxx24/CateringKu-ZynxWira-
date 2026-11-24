<?php

use App\Models\Cart;
use App\Models\Order;
use App\Models\PromoUsage;
use App\Models\User;

beforeEach(function () {
    $this->customer = User::factory()->create();
    $this->vendor = makeVendor();
    $this->item = makeMenuItem($this->vendor, ['price' => 20000]);
});

test('checkout creates the order, its payment, and empties the cart', function () {
    fillCart($this->customer, $this->vendor, $this->item, 2);

    $response = $this->actingAs($this->customer)->post(route('checkout.store'), checkoutPayload($this->vendor, ['num_people' => 10]));

    $order = Order::firstOrFail();
    $response->assertRedirect(route('orders.receipt', $order->order_id));

    // 2 portions x 10 guests x Rp 20.000 = 400.000; PPN 11% = 44.000; delivery 15.000
    expect((float) $order->subtotal)->toBe(400000.0)
        ->and((float) $order->tax)->toBe(44000.0)
        ->and((float) $order->total_amount)->toBe(459000.0)
        ->and($order->items)->toHaveCount(1)
        ->and($order->items->first()->quantity)->toBe(20)
        ->and($order->payments)->toHaveCount(1)
        ->and((float) $order->payments->first()->amount)->toBe(459000.0)
        ->and(Cart::count())->toBe(0);
});

test('prices always come from the database, not from the request', function () {
    fillCart($this->customer, $this->vendor, $this->item);

    $this->actingAs($this->customer)->post(route('checkout.store'), checkoutPayload($this->vendor, [
        'total_amount' => 1, 'subtotal' => 1, 'tax' => 0, 'delivery_fee' => 0,
    ]));

    expect((float) Order::first()->total_amount)->toBe(237000.0); // 200.000 + PPN 22.000 + delivery 15.000
});

test('an unknown payment method or a mismatched provider is rejected', function () {
    fillCart($this->customer, $this->vendor, $this->item);

    $this->actingAs($this->customer)
        ->post(route('checkout.store'), checkoutPayload($this->vendor, ['payment_method' => 'bitcoin']))
        ->assertSessionHasErrors('payment_method');

    $this->actingAs($this->customer)
        ->post(route('checkout.store'), checkoutPayload($this->vendor, ['payment_method' => 'qris', 'payment_provider' => 'bca']))
        ->assertSessionHasErrors('payment_provider');

    expect(Order::count())->toBe(0);
});

test('the event date must be in the future', function () {
    fillCart($this->customer, $this->vendor, $this->item);

    $this->actingAs($this->customer)
        ->post(route('checkout.store'), checkoutPayload($this->vendor, ['event_date' => now()->toDateString()]))
        ->assertSessionHasErrors('event_date');
});

test('an unavailable item blocks checkout', function () {
    fillCart($this->customer, $this->vendor, $this->item);
    $this->item->update(['is_available' => false]);

    $this->actingAs($this->customer)
        ->post(route('checkout.store'), checkoutPayload($this->vendor))
        ->assertSessionHasErrors('vendor_id');

    expect(Order::count())->toBe(0);
});

test('a promo code lowers the total and is counted exactly once', function () {
    $promo = makePromo(['promo_code' => 'HEMAT10', 'discount_value' => 10, 'usage_limit' => 5]);
    fillCart($this->customer, $this->vendor, $this->item);

    $this->actingAs($this->customer)
        ->post(route('checkout.store'), checkoutPayload($this->vendor, ['promo_code' => 'hemat10']))
        ->assertSessionHasNoErrors();

    $order = Order::firstOrFail();
    // subtotal 200.000, discount 20.000, PPN on 180.000 = 19.800, delivery 15.000
    expect((float) $order->discount)->toBe(20000.0)
        ->and((float) $order->total_amount)->toBe(214800.0)
        ->and($promo->fresh()->used_count)->toBe(1)
        ->and(PromoUsage::where('order_id', $order->order_id)->count())->toBe(1);
});

test('a promo cannot be reused by the same customer', function () {
    makePromo();
    fillCart($this->customer, $this->vendor, $this->item);
    $this->actingAs($this->customer)->post(route('checkout.store'), checkoutPayload($this->vendor, ['promo_code' => 'HEMAT10']));

    fillCart($this->customer, $this->vendor, $this->item);
    $this->actingAs($this->customer)
        ->post(route('checkout.store'), checkoutPayload($this->vendor, ['promo_code' => 'HEMAT10']))
        ->assertSessionHasErrors('promo_code');

    expect(Order::count())->toBe(1);
});

test('an exhausted or expired promo is refused', function () {
    makePromo(['promo_code' => 'HABIS', 'usage_limit' => 1, 'used_count' => 1]);
    makePromo(['promo_code' => 'LAWAS', 'valid_until' => now()->subDay()]);
    fillCart($this->customer, $this->vendor, $this->item);

    foreach (['HABIS', 'LAWAS'] as $code) {
        $this->actingAs($this->customer)
            ->post(route('checkout.store'), checkoutPayload($this->vendor, ['promo_code' => $code]))
            ->assertSessionHasErrors('promo_code');
    }

    expect(Order::count())->toBe(0);
});

test('a promo for another vendor does not apply', function () {
    $other = makeVendor(['vendor_name' => 'Dapur Lain']);
    makePromo(['promo_code' => 'LAIN', 'vendor_id' => $other->vendor_id]);
    fillCart($this->customer, $this->vendor, $this->item);

    $this->actingAs($this->customer)
        ->post(route('checkout.store'), checkoutPayload($this->vendor, ['promo_code' => 'LAIN']))
        ->assertSessionHasErrors('promo_code');
});

test('the discount never exceeds the subtotal', function () {
    makePromo(['promo_code' => 'GEDE', 'discount_type' => 'fixed', 'discount_value' => 9_000_000]);
    fillCart($this->customer, $this->vendor, $this->item);

    $this->actingAs($this->customer)->post(route('checkout.store'), checkoutPayload($this->vendor, ['promo_code' => 'GEDE']));

    $order = Order::firstOrFail();
    expect((float) $order->discount)->toBe((float) $order->subtotal)
        ->and((float) $order->total_amount)->toBe(15000.0);
});

test('the promo preview endpoint uses the server-side cart total', function () {
    makePromo(['promo_code' => 'MINIM', 'min_order' => 500000]);
    fillCart($this->customer, $this->vendor, $this->item);

    // The browser claims a huge order_amount; the server must ignore it and see the real Rp 200.000.
    $this->actingAs($this->customer)
        ->postJson(route('promo.validate'), ['code' => 'MINIM', 'vendor_id' => $this->vendor->vendor_id, 'num_people' => 10, 'order_amount' => 99_000_000])
        ->assertStatus(422)
        ->assertJson(['valid' => false]);

    makePromo(['promo_code' => 'OKE']);
    $this->actingAs($this->customer)
        ->postJson(route('promo.validate'), ['code' => 'oke', 'vendor_id' => $this->vendor->vendor_id, 'num_people' => 10])
        ->assertOk()
        ->assertJson(['valid' => true, 'discount' => 20000]);
});

test('order numbers stay unique even after an order is deleted', function () {
    fillCart($this->customer, $this->vendor, $this->item);
    $this->actingAs($this->customer)->post(route('checkout.store'), checkoutPayload($this->vendor));
    fillCart($this->customer, $this->vendor, $this->item);
    $this->actingAs($this->customer)->post(route('checkout.store'), checkoutPayload($this->vendor));

    Order::orderBy('order_id')->first()->delete();

    fillCart($this->customer, $this->vendor, $this->item);
    $this->actingAs($this->customer)->post(route('checkout.store'), checkoutPayload($this->vendor))->assertSessionHasNoErrors();

    expect(Order::pluck('order_number')->unique())->toHaveCount(2);
});
