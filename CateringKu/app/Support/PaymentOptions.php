<?php

namespace App\Support;

/**
 * Payment methods and providers accepted at checkout.
 * Keep in sync with `paymentCategories` in resources/js/pages/Checkout.vue.
 */
final class PaymentOptions
{
    public const PROVIDERS = [
        'bank_transfer' => ['bca', 'bni', 'bri', 'mandiri'],
        'e-wallet' => ['dana', 'gopay', 'ovo', 'shopeepay'],
        'qris' => ['qris'],
        'virtual_account' => ['bca_va', 'bni_va', 'bri_va', 'mandiri_va'],
    ];

    /** @return list<string> */
    public static function methods(): array
    {
        return array_keys(self::PROVIDERS);
    }

    /** @return list<string> */
    public static function providersFor(?string $method): array
    {
        return self::PROVIDERS[$method] ?? [];
    }
}
