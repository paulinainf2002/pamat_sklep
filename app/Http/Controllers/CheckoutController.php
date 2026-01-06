<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
public function applyCoupon(Request $request)
{
    $code = trim($request->coupon);

    // PRZYKŁADOWE kody – zmienisz na swoje
    $coupons = [
        'RABAT10' => 0.10,
        'RABAT15' => 0.15,
        'JESIEN5'  => 0.05,
    ];

    if (! array_key_exists($code, $coupons)) {
        return redirect()->route('cart.index')
            ->with('error', 'Niepoprawny kod rabatowy.');
    }

    session([
        'coupon_code' => $code,
        'coupon'      => $coupons[$code],
    ]);

    return redirect()->route('cart.index')
        ->with('success', "Zastosowano kod: {$code}");
}


    /**
     * ----------------------------------------
     * 1) SUMMARY — działa dla GET i POST
     * ----------------------------------------
     */
public function summary(Request $request)
{
    // Jeśli wchodzimy z POST (z koszyka) – zapisz dane do sesji
    if ($request->isMethod('post')) {
        session([
            'checkout_name'             => $request->name,
            'checkout_email'            => $request->email,
            'checkout_phone'            => $request->phone,
            'checkout_delivery_method'  => $request->delivery_method,
            'checkout_payment_method'   => $request->payment_method,
            'coupon_code'               => $request->coupon,
        ]);

        if ($request->delivery_method === 'inpost') {
            session([
                'inpost_point' => $request->delivery_point,
            ]);
        }

        if ($request->delivery_method === 'kurier') {
            session([
                'checkout_address'     => $request->address,
                'checkout_city'        => $request->city,
                'checkout_postal_code' => $request->postal_code,
            ]);
        }
    }

    $cart = session('cart', []);
    if (empty($cart)) {
        return redirect()->route('cart.index')
            ->with('error', 'Koszyk jest pusty!');
    }

    $productsTotal = array_sum(array_column($cart, 'price'));
    $couponPercent = session('coupon', 0);
    $discount      = $productsTotal * $couponPercent;

    $deliveryMethod = session('checkout_delivery_method', 'inpost');
    $shipping       = $deliveryMethod === 'inpost' ? 11.99 : 14.99;

    $final = $productsTotal - $discount + $shipping;

    return view('checkout.summary', [
        'cart'          => $cart,
        'productsTotal' => $productsTotal,
        'discount'      => $discount,
        'shipping'      => $shipping,
        'final'         => $final,
    ]);
}


    /**
     * ----------------------------------------
     * 2) PLACE ORDER — tworzenie zamówienia
     * ----------------------------------------
     */
    public function placeOrder(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Koszyk jest pusty!');
        }

        // Walidacja podstawowych danych
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email',
            'phone'           => 'required|string|max:20',
            'delivery_method' => 'required|in:inpost,kurier',
            'payment_method'  => 'required|in:p24,transfer',
        ]);

        // Dodatkowa walidacja
        if ($request->delivery_method === 'inpost') {
            $request->validate([
                'delivery_point' => 'required|string|max:255',
            ]);
        }

        if ($request->delivery_method === 'kurier') {
            $request->validate([
                'address'     => 'required|string|max:255',
                'city'        => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
            ]);
        }

        // Liczenie sum
        $productsTotal = array_sum(array_column($cart, 'price'));
        $couponPercent = session('coupon', 0);
        $discount      = $productsTotal * $couponPercent;
        $productsTotal = $productsTotal - $discount;

        $shipping = $request->delivery_method === 'inpost' ? 11.99 : 14.99;
        $finalTotal = $productsTotal + $shipping;


        // Generowanie numeru zamówienia
        $orderNumber = 'PMT-' . now()->format('Y') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

        // Tworzenie zamówienia
        // $order = Order::create([
        //     'order_number'     => $orderNumber,
        //     'name'             => $request->name,
        //     'email'            => $request->email,
        //     'phone'            => $request->phone,

        //     'address'          => $request->address ?? null,
        //     'city'             => $request->city ?? null,
        //     'postal_code'      => $request->postal_code ?? null,

        //     'delivery_method'  => $request->delivery_method,
        //     'delivery_point'   => $request->delivery_point ?? null,
        //     'shipping_price'   => $shipping,

        //     'payment_method'   => $request->payment_method,
        //     'payment_status'   => 'pending',

        //     'total'            => $finalTotal,
        //     'status'           => 'pending',
        // ]);
        $order = Order::create([
            'order_number' => $orderNumber,
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,

            // -------------------------------
            // 🔥 DOSTAWA — różne pola zależnie od metody
            // -------------------------------
            'delivery_method' => $request->delivery_method,
            'delivery_point'  => $request->delivery_method === 'inpost'
                                ? $request->delivery_point
                                : null,

            'address'     => $request->delivery_method === 'kurier'
                                ? $request->address
                                : null,
            'city'        => $request->delivery_method === 'kurier'
                                ? $request->city
                                : null,
            'postal_code' => $request->delivery_method === 'kurier'
                                ? $request->postal_code
                                : null,

            // 'shipping_price' => $shippingPrice,

            // -------------------------------
            // PŁATNOŚĆ
            // -------------------------------
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',

            // -------------------------------
            // SUMA
            // -------------------------------
            'total' => $finalTotal,
            'status' => 'pending',
        ]);


        // Tworzenie pozycji zamówienia
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
            ]);
        }

        // // Wyczyść koszyk
        // session()->forget('cart');

        // // Redirect płatności
        // if ($request->payment_method === 'p24') {
        //     return redirect()->route('p24.redirect', ['order' => $order->id]);
        // }

        // return redirect()->route('checkout.success', ['order' => $order->order_number]);
        // Redirect płatności
    if ($request->payment_method === 'p24') {
        // NIE czyścimy koszyka tutaj – bo płatność może się nie udać
        // Koszyk czyścimy dopiero po potwierdzeniu (return, gdy status jest paid)
        return redirect()->route('p24.redirect', ['order' => $order->id]);
    }

    // dla przelewu tradycyjnego możemy czyścić od razu
    session()->forget('cart');
    session()->forget('coupon');
    session()->forget('coupon_code');

    return redirect()->route('checkout.success', ['order' => $order->order_number]);

    }

    /**
     * ----------------------------------------
     * 3) SUKCES
     * ----------------------------------------
     */
    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('checkout.success', compact('order'));
    }
}
