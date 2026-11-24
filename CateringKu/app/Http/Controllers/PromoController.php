<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\OrderPricing;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PromoController extends Controller
{
    public function __construct(private readonly OrderPricing $pricing) {}

    /**
     * Preview a promo code against the signed-in user's own cart.
     * The amount is computed on the server; any total sent by the browser is ignored.
     */
    public function validate_(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50',
            'vendor_id' => 'required|integer|exists:vendors,vendor_id',
            'num_people' => 'nullable|integer|min:1|max:10000',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('vendor_id', $data['vendor_id'])
            ->with(['items.menuItem', 'items.package'])
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json(['valid' => false, 'message' => 'Keranjang kosong.'], 422);
        }

        try {
            $quote = $this->pricing->quote($cart, (int) ($data['num_people'] ?? 1), $data['code'], $request->user()->id);
        } catch (ValidationException $e) {
            return response()->json(['valid' => false, 'message' => $e->errors()['promo_code'][0]], 422);
        }

        return response()->json([
            'valid' => true,
            'promo' => $quote['promo']->only(['promo_code', 'promo_name', 'description']),
            'discount' => $quote['discount'],
            'total' => $quote['total'],
        ]);
    }
}
