<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * STRONA PODSUMOWANIA (czyta dane z sesji + koszyk)
     */
    public function summary()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Koszyk jest pusty!');
        }

        $total = array_sum(array_column($cart, 'price'));

        return view('checkout.summary', [
            'cart'  => $cart,
            'total' => $total,
        ]);
    }

    /**
     * ZŁOŻENIE ZAMÓWIENIA – WSZYSTKO Z SESJI, NIE Z REQUESTU
     */
    public function placeOrder(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Koszyk jest pusty!');
        }

        // Dane klienta z sesji (zapisywane przez updateField())
        $name        = session('checkout_name');
        $email       = session('checkout_email');
        $phone       = session('checkout_phone');

        $address     = session('checkout_address');
        $city        = session('checkout_city');
        $postalCode  = session('checkout_postal_code');

        $deliveryMethod = session('checkout_delivery_method', 'inpost');   // inpost / kurier
        $paymentMethod  = session('checkout_payment_method', 'p24');       // p24 / transfer

        $lockerPoint    = session('inpost_point'); // zapisane w saveLocker()

        // ------------------ Walidacja "ręczna" na podstawie sesji ------------------

        if (!$name || !$email || !$phone) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Uzupełnij dane klienta (imię, e-mail, telefon).');
        }

        if ($deliveryMethod === 'inpost') {
            if (!$lockerPoint) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Wybierz paczkomat InPost.');
            }
            $shippingPrice   = 11.99;
            $deliveryAddress = null;
            $deliveryPoint   = $lockerPoint;
        } elseif ($deliveryMethod === 'kurier') {
            if (!$address || !$city || !$postalCode) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Uzupełnij adres dostawy dla kuriera.');
            }
            $shippingPrice   = 14.99;
            $deliveryAddress = $address . ', ' . $postalCode . ' ' . $city;
            $deliveryPoint   = null;
        } else {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Nieprawidłowa metoda dostawy.');
        }

        if (!in_array($paymentMethod, ['p24', 'transfer'])) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Nieprawidłowa metoda płatności.');
        }

        // ------------------ Suma koszyka ------------------
        $productsTotal = array_sum(array_column($cart, 'price'));
        $finalTotal    = $productsTotal + $shippingPrice;

        // ------------------ Numer zamówienia ------------------
        $orderNumber = 'PMT-' . now()->format('Y') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

        // ------------------ Tworzenie zamówienia ------------------
        $order = Order::create([
            'order_number'     => $orderNumber,
            'name'             => $name,
            'email'            => $email,
            'phone'            => $phone,

            'address'          => $deliveryMethod === 'kurier' ? $address : null,
            'city'             => $deliveryMethod === 'kurier' ? $city : null,
            'postal_code'      => $deliveryMethod === 'kurier' ? $postalCode : null,

            'delivery_method'  => $deliveryMethod,
            'delivery_point'   => $deliveryPoint,
            'delivery_address' => $deliveryAddress,
            'shipping_price'   => $shippingPrice,

            'payment_method'   => $paymentMethod,
            'payment_status'   => 'pending',

            'total'            => $finalTotal,
            'status'           => 'pending',
        ]);

        // ------------------ Pozycje zamówienia ------------------
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
            ]);
        }

        // ------------------ Czyszczenie koszyka ------------------
        session()->forget('cart');

        // (opcjonalnie) czyścimy również dane checkoutu
        // session()->forget([
        //     'checkout_name','checkout_email','checkout_phone',
        //     'checkout_address','checkout_city','checkout_postal_code',
        //     'checkout_delivery_method','checkout_payment_method',
        //     'inpost_point','inpost_point_id','inpost_point_full',
        // ]);

        // ------------------ Rozgałęzienie płatności ------------------

        if ($paymentMethod === 'p24') {
            // tutaj później podepniemy realną integrację z Przelewy24
            return redirect()->route('p24.redirect', ['order' => $order->id]);
        }

        // Przelew tradycyjny → prosta strona z sukcesem / danymi do przelewu
        return redirect()->route('checkout.success', [
            'order' => $order->order_number,
        ]);
    }

    /**
     * Strona sukcesu (po przelewie tradycyjnym lub udanym P24)
     */
    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('checkout.success', [
            'order' => $order,
        ]);
    }

    /**
     * AJAX – zapisywanie pól checkoutu (wywoływane w koszyku)
     */
    public function updateField(Request $request)
    {
        $field = $request->field;
        $value = $request->value;

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

        session(["checkout_{$field}" => $value]);

        return response()->json(['success' => true]);
    }

    /**
     * AJAX – zapis wybranego paczkomatu (geowidget InPost)
     */
    public function saveLocker(Request $request)
    {
        session()->put('inpost_point', $request->locker);
        session()->put('inpost_point_id', $request->locker_id);
        session()->put('inpost_point_full', $request->locker_full);

        return response()->json(['status' => 'ok']);
    }
}
