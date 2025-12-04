<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * 1) ZASTOSOWANIE KODU RABATOWEGO
     *    - wywoływane z formularza w koszyku (POST /apply-coupon)
     */
    public function applyCoupon(Request $request)
    {
        $code = trim($request->coupon);

        // PRZYKŁADOWE kody – możesz zmienić na swoje
        $coupons = [
            'RABAT10' => 0.10,
            'RABAT15' => 0.15,
        ];

        if ($code === '' || ! isset($coupons[$code])) {
            // usuwamy kupon z sesji
            session()->forget(['coupon', 'coupon_code']);

            return redirect()
                ->route('cart.index')
                ->with('success', 'Nieprawidłowy kod rabatowy – usunięto kupon.');
        }

        session()->put('coupon', $coupons[$code]);
        session()->put('coupon_code', $code);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Zastosowano kod rabatowy: ' . $code);
    }

    /**
     * 2) ZAPIS POLA Z KOSZYKA (AJAX)
     *    - updateField(field, value) z JS
     */
    public function updateField(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');

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

        if (! in_array($field, $allowed, true)) {
            return response()->json(['error' => 'Field not allowed'], 422);
        }

        // zapisujemy pod kluczem checkout_<pole>, tak jak używasz w widokach
        session(['checkout_' . $field => $value]);

        return response()->json(['success' => true]);
    }

    /**
     * 3) ZAPIS PUNKTU PACZKOMATU (InPost GeoWidget)
     *    - wywoływane z afterPointSelected()
     */
    public function saveLocker(Request $request)
    {
        session()->put('inpost_point', $request->locker);
        session()->put('inpost_point_id', $request->locker_id);
        session()->put('inpost_point_full', $request->locker_full);

        return response()->json(['status' => 'ok']);
    }

    /**
     * 4) STRONA PODSUMOWANIA (GET /checkout/summary)
     *    - odczytuje koszyk + dane z sesji
     */
    public function summary()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Koszyk jest pusty!');
        }

        // suma produktów
        $productsTotal = array_sum(array_column($cart, 'price'));

        // kupon
        $couponPercent = session('coupon', 0);
        $discount      = $productsTotal * $couponPercent;

        // dostawa wg wybranej metody
        $deliveryMethod = session('checkout_delivery_method', 'inpost');
        $shipping       = $deliveryMethod === 'kurier' ? 14.99 : 11.99;

        $finalTotal = $productsTotal - $discount + $shipping;

        return view('checkout.summary', [
            'cart'          => $cart,
            'productsTotal' => $productsTotal,
            'discount'      => $discount,
            'shipping'      => $shipping,
            'finalTotal'    => $finalTotal,
        ]);
    }

    /**
     * 5) ZŁOŻENIE ZAMÓWIENIA (POST /checkout/place-order)
     *    - dane bierzemy z SESJI, nie polegamy na formularzu
     */
    public function placeOrder(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Koszyk jest pusty!');
        }

        // ------------------ Dane klienta z sesji ------------------
        $name   = session('checkout_name');
        $email  = session('checkout_email');
        $phone  = session('checkout_phone');

        if (! $name || ! $email || ! $phone) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Uzupełnij dane klienta (imię i nazwisko, e-mail, telefon).');
        }

        // ------------------ Dostawa z sesji ------------------
        $deliveryMethod = session('checkout_delivery_method', 'inpost');
        $paymentMethod  = session('checkout_payment_method', 'p24');

        // dane paczkomatu
        $lockerPoint = session('inpost_point');

        // dane kuriera
        $address     = session('checkout_address');
        $city        = session('checkout_city');
        $postalCode  = session('checkout_postal_code');

        // domyślne wartości, żeby NIE było "Undefined variable"
        $shippingPrice   = 0.0;
        $deliveryAddress = '';
        $deliveryPoint   = null;

        // walidacja dostawy
        if ($deliveryMethod === 'inpost') {
            if (! $lockerPoint) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Wybierz paczkomat InPost.');
            }

            $shippingPrice = 11.99;
            $deliveryPoint = $lockerPoint;

            // kolumna address w DB jest NOT NULL → wstawiamy tekst z paczkomatu
            if (! $address) {
                $address = $lockerPoint;
            }

        } elseif ($deliveryMethod === 'kurier') {

            if (! $address || ! $city || ! $postalCode) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Uzupełnij adres dostawy dla kuriera.');
            }

            $shippingPrice   = 14.99;
            $deliveryAddress = $address . ', ' . $postalCode . ' ' . $city;

        } else {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Nieprawidłowa metoda dostawy.');
        }

        // walidacja płatności
        if (! in_array($paymentMethod, ['p24', 'transfer'], true)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Nieprawidłowa metoda płatności.');
        }

        // ------------------ Suma koszyka + rabat ------------------
        $productsTotal = array_sum(array_column($cart, 'price'));
        $couponPercent = session('coupon', 0);
        $discount      = $productsTotal * $couponPercent;
        $finalTotal    = $productsTotal - $discount + $shippingPrice;

        // ------------------ Numer zamówienia ------------------
        $orderNumber = 'PMT-' . now()->format('Y') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

        // ------------------ Tworzenie zamówienia ------------------
        $order = Order::create([
            'order_number'     => $orderNumber,
            'name'             => $name,
            'email'            => $email,
            'phone'            => $phone,

            // kolumny adresowe – dla InPost użyjemy paczkomatu jako address
            'address'          => $address ?? '',
            'city'             => $city ?? '',
            'postal_code'      => $postalCode ?? '',

            'delivery_method'  => $deliveryMethod,
            'delivery_point'   => $deliveryPoint,
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
                'price'      => $item['price'], // cena za całą pozycję
            ]);
        }

        // ------------------ Wyczyszczenie koszyka ------------------
        session()->forget('cart');
        // Jeśli chcesz, możesz też wyczyścić dane checkoutu:
        // session()->forget([
        //     'checkout_name', 'checkout_email', 'checkout_phone',
        //     'checkout_address', 'checkout_city', 'checkout_postal_code',
        //     'checkout_delivery_method', 'checkout_payment_method',
        //     'inpost_point', 'inpost_point_id', 'inpost_point_full',
        //     'coupon', 'coupon_code',
        // ]);

        // ------------------ Rozgałęzienie płatności ------------------
        if ($paymentMethod === 'p24') {
            // tu później podłączysz prawdziwe Przelewy24
            return redirect()->route('p24.redirect', ['order' => $order->id]);
        }

        // przelew tradycyjny → strona sukcesu
        return redirect()->route('checkout.success', [
            'order' => $order->order_number,
        ]);
    }

    /**
     * 6) SUKCES – po przelewie tradycyjnym / udanym P24
     */
    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('checkout.success', compact('order'));
    }
}
