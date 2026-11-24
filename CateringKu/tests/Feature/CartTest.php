<?php

use App\Models\CartItem;
use App\Models\User;

beforeEach(function () {
    $this->customer = User::factory()->create();
    $this->vendor = makeVendor();
    $this->item = makeMenuItem($this->vendor);
});

test('a customer can add a menu item to the cart', function () {
    $this->actingAs($this->customer)
        ->post(route('cart.add'), ['vendor_id' => $this->vendor->vendor_id, 'item_id' => $this->item->item_id, 'quantity' => 2])
        ->assertSessionHasNoErrors();

    expect(CartItem::first()->quantity)->toBe(2);
});

test('adding the same item twice merges the quantity', function () {
    $payload = ['vendor_id' => $this->vendor->vendor_id, 'item_id' => $this->item->item_id, 'quantity' => 1];

    $this->actingAs($this->customer)->post(route('cart.add'), $payload);
    $this->actingAs($this->customer)->post(route('cart.add'), $payload);

    expect(CartItem::count())->toBe(1)->and(CartItem::first()->quantity)->toBe(2);
});

test('an item cannot be added to another vendor\'s cart', function () {
    $otherVendor = makeVendor(['vendor_name' => 'Dapur Lain']);

    $this->actingAs($this->customer)
        ->post(route('cart.add'), ['vendor_id' => $otherVendor->vendor_id, 'item_id' => $this->item->item_id, 'quantity' => 1])
        ->assertSessionHasErrors('item_id');

    expect(CartItem::count())->toBe(0);
});

test('an unavailable item cannot be added', function () {
    $this->item->update(['is_available' => false]);

    $this->actingAs($this->customer)
        ->post(route('cart.add'), ['vendor_id' => $this->vendor->vendor_id, 'item_id' => $this->item->item_id, 'quantity' => 1])
        ->assertSessionHasErrors('item_id');
});

test('either an item or a package is required, not neither', function () {
    $this->actingAs($this->customer)
        ->post(route('cart.add'), ['vendor_id' => $this->vendor->vendor_id, 'quantity' => 1])
        ->assertSessionHasErrors(['item_id', 'package_id']);
});

test('quantity is capped', function () {
    $this->actingAs($this->customer)
        ->post(route('cart.add'), ['vendor_id' => $this->vendor->vendor_id, 'item_id' => $this->item->item_id, 'quantity' => 1001])
        ->assertSessionHasErrors('quantity');
});

test('a customer cannot change someone else\'s cart item', function () {
    $stranger = User::factory()->create();
    $cart = fillCart($stranger, $this->vendor, $this->item);
    $cartItem = $cart->items()->first();

    $this->actingAs($this->customer)->patch(route('cart.update', $cartItem->cart_item_id), ['quantity' => 5])->assertForbidden();
    $this->actingAs($this->customer)->delete(route('cart.remove', $cartItem->cart_item_id))->assertForbidden();

    expect($cartItem->fresh()->quantity)->toBe(1);
});

test('guests are sent to login', function () {
    $this->get(route('cart.index'))->assertRedirect(route('login'));
});
