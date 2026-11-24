<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('the category filter only returns vendors that sell that category', function () {
    $boxes = Category::create(['category_name' => 'Nasi Kotak']);
    $sellsBoxes = makeVendor(['vendor_name' => 'Kotak Jaya']);
    $other = makeVendor(['vendor_name' => 'Hanya Minuman']);
    makeMenuItem($sellsBoxes, ['category_id' => $boxes->category_id]);
    makeMenuItem($other);

    $this->get('/search?type=nasi-kotak')->assertInertia(fn (Assert $page) => $page
        ->component('Search')
        ->where('activeCategory', 'Nasi Kotak')
        ->has('vendors.data', 1)
        ->where('vendors.data.0.vendor_name', 'Kotak Jaya'));
});

test('an unknown category shows an empty result instead of every vendor', function () {
    makeMenuItem(makeVendor());

    $this->get('/search?type=tidak-ada')->assertInertia(fn (Assert $page) => $page
        ->where('activeCategory', null)
        ->has('vendors.data', 0));
});

test('the about page shows live numbers from the database', function () {
    $vendor = makeVendor(['city' => 'Bandung']);
    makeVendor(['city' => 'Bandung', 'status' => 'pending']);
    Order::create([
        'user_id' => User::factory()->create()->id, 'vendor_id' => $vendor->vendor_id, 'order_number' => 'ORD-T-1',
        'order_type' => 'custom', 'event_date' => now()->addDay(), 'event_time' => '10:00', 'delivery_address' => 'x',
        'num_people' => 1, 'subtotal' => 1, 'total_amount' => 1, 'status' => 'completed',
    ]);

    $this->get('/about')->assertInertia(fn (Assert $page) => $page
        ->component('About')
        ->where('stats.vendors', 1)
        ->where('stats.completedOrders', 1)
        ->where('stats.cities', 1)
        ->where('stats.averageRating', null));
});

test('contact details stay hidden until the owner configures them', function () {
    $this->get('/')->assertInertia(fn (Assert $page) => $page->where('site.contact', [])->where('site.social', []));

    config(['cateringku.contact.phone' => '0812-0000-0000']);

    $this->get('/')->assertInertia(fn (Assert $page) => $page->where('site.contact.phone', '0812-0000-0000'));
});
