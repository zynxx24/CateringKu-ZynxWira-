<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promo;
use App\Models\PromoUsage;
use App\Services\OrderPricing;
use App\Support\PaymentOptions;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function __construct(private readonly OrderPricing $pricing) {}

    public function index(Request $request)
    {
        $carts = Cart::where('user_id', $request->user()->id)
            ->with(['vendor', 'items.menuItem', 'items.package'])
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        return Inertia::render('Checkout', [
            'carts' => $carts,
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'event_date' => 'required|date|after:today',
            'event_time' => 'required|date_format:H:i',
            'delivery_address' => 'required|string|max:500',
            'delivery_city' => 'nullable|string|max:50',
            'num_people' => 'required|integer|min:1|max:10000',
            'event_type' => 'nullable|string|max:50',
            'special_request' => 'nullable|string|max:1000',
            'payment_method' => ['required', 'string', Rule::in(PaymentOptions::methods())],
            'payment_provider' => ['required', 'string', Rule::in(PaymentOptions::providersFor($request->input('payment_method')))],
            'promo_code' => 'nullable|string|max:50',
        ], [
            'event_date.after' => 'Tanggal acara harus setelah hari ini. Silakan pilih tanggal yang akan datang.',
            'event_time.date_format' => 'Format jam acara tidak valid.',
            'num_people.min' => 'Jumlah tamu minimal 1 orang.',
            'num_people.max' => 'Jumlah tamu maksimal 10.000 orang.',
            'delivery_address.required' => 'Alamat pengiriman wajib diisi.',
            'payment_method.required' => 'Silakan pilih metode pembayaran.',
            'payment_method.in' => 'Metode pembayaran tidak tersedia.',
            'payment_provider.required' => 'Silakan pilih provider pembayaran.',
            'payment_provider.in' => 'Provider tidak cocok dengan metode pembayaran.',
        ]);

        $userId = $request->user()->id;
        $numPeople = (int) $data['num_people'];

        $cart = Cart::where('user_id', $userId)
            ->where('vendor_id', $data['vendor_id'])
            ->with(['items.menuItem', 'items.package'])
            ->firstOrFail();

        $this->assertCartIsOrderable($cart);

        // Authoritative totals: computed here from database prices, never from the browser.
        $quote = $this->pricing->quote($cart, $numPeople, $data['promo_code'] ?? null, $userId);

        try {
            $order = DB::transaction(fn () => $this->placeOrder($data, $cart, $quote, $userId));
        } catch (QueryException $e) {
            // A database failure is the one error the customer can retry; anything else should surface.
            report($e);

            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.')->withInput();
        }

        return redirect()->route('orders.receipt', $order->order_id)->with('success', 'Pesanan berhasil dibuat!');
    }

    /** Write the order, its lines, payment and promo usage, then empty the cart. Runs inside a transaction. */
    private function placeOrder(array $data, Cart $cart, array $quote, int $userId): Order
    {
        $numPeople = (int) $data['num_people'];

        if ($quote['promo']) {
            $this->redeemPromo($quote['promo']);
        }

        $order = Order::create([
            'user_id' => $userId,
            'vendor_id' => $cart->vendor_id,
            'order_number' => Order::generateOrderNumber(),
            'order_type' => 'custom',
            'event_type' => $data['event_type'] ?? null,
            'event_date' => $data['event_date'],
            'event_time' => $data['event_time'],
            'delivery_address' => $data['delivery_address'],
            'delivery_city' => $data['delivery_city'] ?? null,
            'num_people' => $numPeople,
            'subtotal' => $quote['subtotal'],
            'tax' => $quote['tax'],
            'delivery_fee' => $quote['delivery_fee'],
            'discount' => $quote['discount'],
            'total_amount' => $quote['total'],
            'special_request' => $data['special_request'] ?? null,
        ]);

        foreach ($cart->items as $item) {
            $unitPrice = $this->pricing->unitPrice($item);
            $quantity = $item->quantity * $numPeople;

            OrderItem::create([
                'order_id' => $order->order_id,
                'item_id' => $item->item_id,
                'package_id' => $item->package_id,
                'item_name' => $item->menuItem?->item_name ?? $item->package?->package_name ?? '',
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $unitPrice * $quantity,
                'notes' => $item->notes,
            ]);
        }

        $order->payments()->create([
            'payment_method' => $data['payment_method'],
            'payment_provider' => $data['payment_provider'],
            'amount' => $quote['total'],
        ]);

        if ($quote['promo']) {
            PromoUsage::create([
                'promo_id' => $quote['promo']->promo_id,
                'user_id' => $userId,
                'order_id' => $order->order_id,
                'discount_amount' => $quote['discount'],
            ]);
        }

        $cart->items()->delete();
        $cart->delete();

        return $order;

    }

    /** Refuse to order from a cart that has an unavailable menu item or package. */
    private function assertCartIsOrderable(Cart $cart): void
    {
        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages(['vendor_id' => 'Keranjang untuk vendor ini kosong.']);
        }

        foreach ($cart->items as $item) {
            $product = $item->menuItem ?? $item->package;

            if (! $product || ! $product->is_available) {
                throw ValidationException::withMessages([
                    'vendor_id' => 'Salah satu item di keranjang sudah tidak tersedia. Silakan perbarui keranjang Anda.',
                ]);
            }
        }
    }

    /** Count one use of the promo, atomically, so the usage limit cannot be exceeded by parallel checkouts. */
    private function redeemPromo(Promo $promo): void
    {
        $redeemed = Promo::active()
            ->where('promo_id', $promo->promo_id)
            ->update(['used_count' => DB::raw('used_count + 1')]);

        if (! $redeemed) {
            throw ValidationException::withMessages(['promo_code' => 'Kode promo sudah habis dipakai.']);
        }
    }
}
