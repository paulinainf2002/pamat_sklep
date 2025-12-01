<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function applyCoupon(Request $request)
    {
        $code = trim($request->coupon);

        // Przykładowe kupony
        $validCoupons = [
            'RABAT10' => 0.10,
            'PAMAT15' => 0.15,
            'JESIEN5' => 0.05,
        ];

        if (!isset($validCoupons[$code])) {
            return back()->with('error', 'Kod rabatowy jest nieprawidłowy.');
        }

        session([
            'coupon'      => $validCoupons[$code],
            'coupon_code' => $code
        ]);

        return back()->with('success', 'Kod rabatowy został zastosowany!');
    }


    public function updateShipping(Request $request)
    {
        session(['shipping' => $request->shipping]);
        return response()->json(['status' => 'ok']);
    }


    public function updatePayment(Request $request)
    {
        session(['payment' => $request->payment]);
        return response()->json(['status' => 'ok']);
    }


    public function summary()
    {
        $cart     = session('cart', []);
        $shipping = session('shipping', 'inpost');
        $payment  = session('payment', 'p24');
        $coupon   = session('coupon', 0);

        return view('checkout.summary', compact(
            'cart', 'shipping', 'payment', 'coupon'
        ));
    }
}
