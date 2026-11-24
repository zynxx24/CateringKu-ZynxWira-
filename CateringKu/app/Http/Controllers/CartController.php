<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MenuItem;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::where('user_id', $request->user()->id)
            ->with(['vendor', 'items.menuItem', 'items.package'])
            ->get();

        $totalAmount = 0;
        foreach ($carts as $cart) {
            foreach ($cart->items as $item) {
                if ($item->menuItem) {
                    $totalAmount += $item->menuItem->price * $item->quantity;
                } elseif ($item->package) {
                    $totalAmount += $item->package->price_per_person * $item->quantity;
                }
            }
        }

        return Inertia::render('Cart', [
            'carts' => $carts,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function addItem(Request $request)
    {
        $data = $request->validate([
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'item_id' => 'nullable|required_without:package_id|prohibits:package_id|exists:menu_items,item_id',
            'package_id' => 'nullable|required_without:item_id|prohibits:item_id|exists:packages,package_id',
            'quantity' => 'required|integer|min:1|max:1000',
            'notes' => 'nullable|string|max:255',
        ]);

        // The product must be sold by the vendor whose cart we are filling, and still be on sale.
        $product = isset($data['item_id'])
            ? MenuItem::find($data['item_id'])
            : Package::find($data['package_id']);

        if ($product->vendor_id !== (int) $data['vendor_id'] || ! $product->is_available) {
            throw ValidationException::withMessages([
                'item_id' => 'Item ini tidak tersedia untuk vendor tersebut.',
            ]);
        }

        $cart = Cart::firstOrCreate([
            'user_id' => $request->user()->id,
            'vendor_id' => $data['vendor_id'],
        ]);

        $existingItem = CartItem::where('cart_id', $cart->cart_id)
            ->where('item_id', $data['item_id'] ?? null)
            ->where('package_id', $data['package_id'] ?? null)
            ->first();

        if ($existingItem) {
            $existingItem->update(['quantity' => min(1000, $existingItem->quantity + $data['quantity'])]);
        } else {
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'item_id' => $data['item_id'] ?? null,
                'package_id' => $data['package_id'] ?? null,
                'quantity' => $data['quantity'],
                'notes' => $data['notes'] ?? null,
            ]);
        }

        return back()->with('success', 'Item ditambahkan ke keranjang!');
    }

    public function updateQuantity(Request $request, int $cartItemId)
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:1000']);

        $cartItem = CartItem::findOrFail($cartItemId);
        $cart = Cart::findOrFail($cartItem->cart_id);
        abort_unless($cart->user_id === $request->user()->id, 403);

        if ($request->quantity <= 0) {
            $cartItem->delete();
        } else {
            $cartItem->update(['quantity' => $request->quantity]);
        }

        // Clean up empty carts
        if ($cart->items()->count() === 0) {
            $cart->delete();
        }

        return back();
    }

    public function removeItem(Request $request, int $cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $cart = Cart::findOrFail($cartItem->cart_id);
        abort_unless($cart->user_id === $request->user()->id, 403);

        $cartItem->delete();

        if ($cart->items()->count() === 0) {
            $cart->delete();
        }

        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}
