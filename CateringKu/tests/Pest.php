<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MenuItem;
use App\Models\Promo;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/** A vendor (with its owner account) that is open for business. */
function makeVendor(array $overrides = []): Vendor
{
    $owner = User::factory()->create(['role' => 'vendor']);

    return Vendor::create(array_merge([
        'user_id' => $owner->id,
        'vendor_name' => 'Dapur Uji',
        'address' => 'Jl. Contoh No. 1',
        'city' => 'Jakarta',
        'status' => 'active',
    ], $overrides));
}

function makeMenuItem(Vendor $vendor, array $overrides = []): MenuItem
{
    return MenuItem::create(array_merge([
        'vendor_id' => $vendor->vendor_id,
        'item_name' => 'Nasi Ayam',
        'price' => 20000,
        'is_available' => true,
    ], $overrides));
}

function makePromo(array $overrides = []): Promo
{
    return Promo::create(array_merge([
        'promo_code' => 'HEMAT10',
        'promo_name' => 'Hemat 10%',
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'min_order' => 0,
        'valid_from' => now()->subDay(),
        'valid_until' => now()->addMonth(),
        'is_active' => true,
    ], $overrides));
}

/** Put one menu item in the customer's cart and return the cart. */
function fillCart(User $customer, Vendor $vendor, MenuItem $item, int $quantity = 1): Cart
{
    $cart = Cart::create(['user_id' => $customer->id, 'vendor_id' => $vendor->vendor_id]);
    CartItem::create(['cart_id' => $cart->cart_id, 'item_id' => $item->item_id, 'quantity' => $quantity]);

    return $cart;
}

function checkoutPayload(Vendor $vendor, array $overrides = []): array
{
    return array_merge([
        'vendor_id' => $vendor->vendor_id,
        'event_date' => now()->addDays(3)->toDateString(),
        'event_time' => '12:00',
        'delivery_address' => 'Jl. Merdeka No. 10',
        'num_people' => 10,
        'payment_method' => 'qris',
        'payment_provider' => 'qris',
    ], $overrides);
}
