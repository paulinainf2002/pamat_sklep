<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function summary()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Koszyk jest pusty!');
        }

        $total = array_sum(array_column($cart, 'price'));

        return view('checkout.summary', [
            'cart'  => $cart,
            'total' => $total,
        ]);
    }

    public function placeOrder(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Koszyk jest pusty!');
        }

        //
        // --------------------------
        // Walidacja podstawowa
        // --------------------------
        //
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email',
            'phone'           => 'required|string|max:20',

            'delivery_method' => 'required|in:inpost,kurier',
            'payment_method'  => 'required|in:p24,transfer',
        ]);

        //
        // --------------------------
        // Walidacja zależna od dostawy
        // --------------------------
        //

        // 1) PACZKOMAT INPOST → musi być wybrany punkt
        if ($request->delivery_method === 'inpost') {
            $request->validate([
                'delivery_point' => 'required|string|max:255',
            ]);

            $shippingPrice = 11.99;
            $deliveryAddress = null;
        }

        // 2) KURIER → musi być pełny adres
        if ($request->delivery_method === 'kurier') {
            $request->validate([
                'address'     => 'required|string|max:255',
                'city'        => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
            ]);

            $shippingPrice = 14.99;
            $deliveryAddress = $request->address . ', ' . $request->postal_code . ' ' . $request->city;
        }

        //
        // --------------------------
        // Sumowanie koszyka
        // --------------------------
        //
        $productsTotal = array_sum(array_column($cart, 'price'));
        $finalTotal = $productsTotal + $shippingPrice;

        //
        // --------------------------
        // Generowanie numeru zamówienia
        // --------------------------
        //
        $orderNumber = 'PMT-' . now()->format('Y') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

        //
        // --------------------------
        // Tworzenie zamówienia
        // --------------------------
        //
        $order = Order::create([
            'order_number'      => $orderNumber,
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,

            // podstawowy adres (tylko dla kuriera – InPost nie potrzebuje)
            'address'           => $request->address ?? null,
            'city'              => $request->city ?? null,
            'postal_code'       => $request->postal_code ?? null,

            'total'             => $finalTotal,
            'status'            => 'pending',

            // DOSTAWA
            'delivery_method'   => $request->delivery_method,
            'delivery_point'    => $request->delivery_point ?? null,
            'delivery_address'  => $deliveryAddress,
            'shipping_price'    => $shippingPrice,

            // PŁATNOŚĆ
            'payment_method'    => $request->payment_method,
            'payment_status'    => 'pending', // zaktualizuje P24 callback
        ]);

        //
        // --------------------------
        // Zapis pozycji zamówienia
        // --------------------------
        //
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
            ]);
        }

        //
        // --------------------------
        // Wyczyszczenie koszyka
        // --------------------------
        //
        session()->forget('cart');

        //
        // --------------------------
        // Obsługa płatności
        // --------------------------
        //

        // 1) Przelewy24 → redirect do bramki
        if ($request->payment_method === 'p24') {
            return redirect()->route('p24.redirect', ['order' => $order->id]);
        }

        // 2) Przelew tradycyjny → strona sukcesu
        return redirect()->route('checkout.success', [
            'order' => $order->order_number
        ]);
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('checkout.success', [
            'order' => $order
        ]);
    }
    public function updateField(Request $request)
{
    $field = $request->field;
    $value = $request->value;

    // zabezpieczenie – pozwalamy tylko na określone pola
    $allowed = [
        'name',
        'email',
        'phone',
        'delivery_method',
        'delivery_point',
        'address',
        'city',
        'postal_code',
        'payment_method',
    ];

    if (!in_array($field, $allowed)) {
        return response()->json(['error' => 'Field not allowed'], 422);
    }

    session(["checkout_$field" => $value]);

    return response()->json(['success' => true]);
}

public function saveLocker(Request $request)
{
    session()->put('inpost_point', $request->locker);
    session()->put('inpost_point_id', $request->locker_id);
    session()->put('inpost_point_full', $request->locker_full);

    return response()->json(['status' => 'ok']);
}

}
