<?php

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

function makeOrder(User $customer, $vendor, array $overrides = []): Order
{
    return Order::create(array_merge([
        'user_id' => $customer->id,
        'vendor_id' => $vendor->vendor_id,
        'order_number' => 'ORD-TEST-'.fake()->unique()->numerify('######'),
        'order_type' => 'custom',
        'event_date' => now()->addWeek(),
        'event_time' => '10:00',
        'delivery_address' => 'Jl. Uji',
        'num_people' => 10,
        'subtotal' => 100000,
        'total_amount' => 100000,
    ], $overrides));
}

test('customers only see their own orders', function () {
    $vendor = makeVendor();
    $mine = makeOrder(User::factory()->create(), $vendor);
    $owner = $mine->user;
    $stranger = User::factory()->create();

    $this->actingAs($stranger)->get(route('orders.show', $mine->order_id))->assertNotFound();
    $this->actingAs($stranger)->get(route('orders.receipt', $mine->order_id))->assertNotFound();
    $this->actingAs($stranger)->post(route('orders.cancel', $mine->order_id))->assertNotFound();
    $this->actingAs($owner)->get(route('orders.show', $mine->order_id))->assertOk();
});

test('a customer can cancel a pending order with a reason, but not a confirmed one', function () {
    $customer = User::factory()->create();
    $vendor = makeVendor();
    $pending = makeOrder($customer, $vendor);
    $confirmed = makeOrder($customer, $vendor, ['status' => 'confirmed']);

    $this->actingAs($customer)->post(route('orders.cancel', $pending->order_id), ['reason' => 'Acara batal'])->assertSessionHasNoErrors();
    $this->actingAs($customer)->post(route('orders.cancel', $confirmed->order_id))->assertNotFound();

    expect($pending->fresh()->status)->toBe('cancelled')
        ->and($pending->fresh()->cancellation_reason)->toBe('Acara batal')
        ->and($confirmed->fresh()->status)->toBe('confirmed');
});

test('a vendor cannot open or update another vendor\'s order', function () {
    $vendorA = makeVendor();
    $vendorB = makeVendor(['vendor_name' => 'Dapur B']);
    $order = makeOrder(User::factory()->create(), $vendorB);

    $this->actingAs($vendorA->user)->get(route('vendor.orders.show', $order->order_id))->assertNotFound();
    $this->actingAs($vendorA->user)->patch(route('vendor.orders.status', $order->order_id), ['status' => 'confirmed'])->assertNotFound();
    $this->actingAs($vendorB->user)->patch(route('vendor.orders.status', $order->order_id), ['status' => 'confirmed'])->assertSessionHasNoErrors();

    expect($order->fresh()->status)->toBe('confirmed');
});

test('a vendor cannot edit another vendor\'s menu', function () {
    $vendorA = makeVendor();
    $vendorB = makeVendor(['vendor_name' => 'Dapur B']);
    $item = makeMenuItem($vendorB);

    $this->actingAs($vendorA->user)
        ->patch(route('vendor.menu.update', $item->item_id), ['item_name' => 'Dibajak', 'price' => 1, 'is_available' => true])
        ->assertNotFound();
    $this->actingAs($vendorA->user)->delete(route('vendor.menu.destroy', $item->item_id))->assertNotFound();

    expect($item->fresh()->item_name)->toBe('Nasi Ayam');
});

test('admin and vendor areas are closed to customers and guests', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();
    $this->actingAs($customer)->get(route('vendor.dashboard'))->assertForbidden();
    auth()->logout();
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

test('an admin can verify a payment once', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $order = makeOrder(User::factory()->create(), makeVendor());
    $payment = Payment::create(['order_id' => $order->order_id, 'payment_method' => 'qris', 'payment_provider' => 'qris', 'amount' => 100000]);

    $this->actingAs($admin)->patch(route('admin.payments.verify', $payment->payment_id))->assertSessionHasNoErrors();
    expect($order->fresh()->payment_status)->toBe('paid');

    $this->actingAs($admin)->patch(route('admin.payments.verify', $payment->payment_id))->assertStatus(409);
});

test('registration always creates a customer, never an admin', function () {
    $this->post('/register', [
        'name' => 'Tamu Baru',
        'username' => 'tamu_baru',
        'email' => 'tamu@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin',
    ]);

    expect(User::where('email', 'tamu@example.com')->first()?->role)->toBe('customer');
});

test('security headers are present and unsafe-eval is not allowed outside local development', function () {
    $response = $this->get('/');

    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    expect($response->headers->get('Content-Security-Policy'))->not->toContain('unsafe-eval');
});
