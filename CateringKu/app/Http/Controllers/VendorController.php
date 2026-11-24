<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Review;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::active();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $activeCategory = null;
        if ($request->filled('type')) {
            // `type` is a category slug such as "nasi-kotak"; it narrows vendors to those selling that category.
            $activeCategory = Category::all()->first(fn ($c) => Str::slug($c->category_name) === $request->type);
            $activeCategory
                ? $query->whereHas('menuItems', fn ($q) => $q->available()->where('category_id', $activeCategory->category_id))
                : $query->whereRaw('1 = 0'); // unknown slug: show the empty state, not every vendor
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $sort = $request->get('sort', 'rating');
        $query = match ($sort) {
            'newest' => $query->orderByDesc('created_at'),
            'name' => $query->orderBy('vendor_name'),
            default => $query->orderByDesc('rating')->orderByDesc('total_reviews'),
        };

        $vendors = $query->paginate(12)->withQueryString();
        $categories = Category::all();
        $cities = Vendor::active()->distinct()->pluck('city')->filter()->values();

        return Inertia::render('Search', [
            'vendors' => $vendors,
            'categories' => $categories,
            'cities' => $cities,
            'filters' => $request->only(['q', 'city', 'sort', 'type']),
            'activeCategory' => $activeCategory?->category_name,
        ]);
    }

    public function show(int $id)
    {
        $vendor = Vendor::with(['menuItems' => function ($q) {
            $q->available()->with('category')->orderBy('category_id');
        }, 'packages' => function ($q) {
            $q->where('is_available', true)->with('items');
        }])
            ->findOrFail($id);

        $reviews = Review::where('vendor_id', $id)
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $categories = $vendor->menuItems->pluck('category')->filter()->unique('category_id')->values();

        return Inertia::render('VendorDetail', [
            'vendor' => $vendor,
            'reviews' => $reviews,
            'menuCategories' => $categories,
        ]);
    }
}
