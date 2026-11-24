<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Promo;
use App\Models\PromoUsage;
use Illuminate\Validation\ValidationException;

/**
 * Single source of truth for order totals.
 *
 * Both the promo preview endpoint and checkout go through this class, so the
 * price a customer sees is always the price the server charges. Nothing here
 * trusts amounts sent by the browser.
 */
class OrderPricing
{
    public const TAX_RATE = 0.11;

    public const DELIVERY_FEE = 15000;

    /**
     * @return array{subtotal: int, discount: int, tax: int, delivery_fee: int, total: int, promo: ?Promo}
     */
    public function quote(Cart $cart, int $numPeople, ?string $promoCode = null, ?int $userId = null): array
    {
        $cart->loadMissing(['items.menuItem', 'items.package']);

        $subtotal = 0;
        foreach ($cart->items as $item) {
            $subtotal += $this->unitPrice($item) * $item->quantity * $numPeople;
        }

        $promo = filled($promoCode)
            ? $this->resolvePromo($promoCode, $cart->vendor_id, $subtotal, $userId)
            : null;

        $discount = $promo ? min((int) round($promo->calculateDiscount($subtotal)), $subtotal) : 0;
        $tax = (int) round(($subtotal - $discount) * self::TAX_RATE);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'delivery_fee' => self::DELIVERY_FEE,
            'total' => $subtotal - $discount + $tax + self::DELIVERY_FEE,
            'promo' => $promo,
        ];
    }

    public function unitPrice($cartItem): float
    {
        if ($cartItem->menuItem) {
            return (float) $cartItem->menuItem->price;
        }

        return $cartItem->package ? (float) $cartItem->package->price_per_person : 0.0;
    }

    /**
     * @throws ValidationException when the code cannot be used for this order
     */
    public function resolvePromo(string $code, int $vendorId, int|float $subtotal, ?int $userId): Promo
    {
        $promo = Promo::active()
            ->where('promo_code', strtoupper(trim($code)))
            ->where(fn ($q) => $q->whereNull('vendor_id')->orWhere('vendor_id', $vendorId))
            ->first();

        if (! $promo) {
            throw ValidationException::withMessages([
                'promo_code' => 'Kode promo tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        if ($subtotal < (float) $promo->min_order) {
            throw ValidationException::withMessages([
                'promo_code' => 'Minimal pesanan untuk kode ini Rp '.number_format((float) $promo->min_order, 0, ',', '.').'.',
            ]);
        }

        if ($userId && PromoUsage::where('promo_id', $promo->promo_id)->where('user_id', $userId)->exists()) {
            throw ValidationException::withMessages([
                'promo_code' => 'Kode promo ini sudah pernah Anda gunakan.',
            ]);
        }

        return $promo;
    }
}
