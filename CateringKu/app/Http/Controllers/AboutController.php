<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Models\Vendor;
use Inertia\Inertia;

class AboutController extends Controller
{
    public function index()
    {
        // Live numbers from this installation's database, never marketing figures.
        $averageRating = Review::avg('rating');

        return Inertia::render('About', [
            'stats' => [
                'vendors' => Vendor::active()->count(),
                'completedOrders' => Order::where('status', 'completed')->count(),
                'cities' => Vendor::active()->whereNotNull('city')->distinct()->count('city'),
                'averageRating' => $averageRating ? round((float) $averageRating, 1) : null,
            ],
        ]);
    }
}
