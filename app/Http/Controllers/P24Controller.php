<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Przelewy24\Przelewy24;

class P24Controller extends Controller
{
    private function client(): Przelewy24
    {
        return new Przelewy24([
            'merchantId' => (int) env('P24_MERCHANT_ID'),
            'posId'      => (int) env('P24_POS_ID'),
            'crc'        => env('P24_CRC'),
            'reportKey'  => env('P24_REPORT_KEY'), // klucz API
            'sandbox'    => (bool) env('P24_SANDBOX', false),
        ]);
    }

    /**
     * Start płatności – rejestracja transakcji i redirect do P24
     * sessionId ustawiamy jako order_number → łatwo później znaleźć zamówienie
     */
    public function pay(Order $order)
    {
        // kwota w groszach (int)
        $amount = (int) round(((float) $order->total) * 100);

        // sessionId w P24 – dajemy order_number
        $sessionId = $order->order_number;

        $transaction = $this->client()->transaction([
            'sessionId'   => $sessionId,
            'amount'      => $amount,
            'currency'    => 'PLN',
            'description' => 'Zamówienie PaMat ' . $order->order_number,
            'email'       => $order->email,
            'country'     => 'PL',
            'language'    => 'pl',

            // Return i Status z numerem zamówienia w URL
            'urlReturn'   => route('p24.return', ['orderNumber' => $order->order_number]),
            'urlStatus'   => route('p24.status', ['orderNumber' => $order->order_number]),
        ]);

        return redirect()->away($transaction->getRedirectUrl());
    }

    /**
     * NOTIFY (urlStatus) – P24 woła serwer->serwer, tu robimy verify i oznaczamy zamówienie jako opłacone
     */
    public function status(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $client = $this->client();

        // biblioteka pobiera body notyfikacji
        $notification = $client->receiveNotification();

        // amount musi być spójny (grosze)
        $amount = (int) round(((float) $order->total) * 100);
        $notification->setAmount($amount);

        $response = $client->verify($notification);

        if ($response->getStatus() >= 200 && $response->getStatus() < 300) {
            $order->payment_status = 'paid';
            $order->status = 'paid';
            $order->save();

            return response('OK', 200);
        }

        return response('VERIFY_FAILED', 400);
    }

    /**
     * RETURN – user wraca po płatności
     * UWAGA: return nie jest źródłem prawdy, ale tu czyścimy koszyk w sesji jeśli zamówienie jest opłacone
     */
    public function return(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // jeśli opłacone → czyścimy koszyk w SESJI użytkownika (tu to ma sens)
        if ($order->payment_status === 'paid') {
            session()->forget('cart');
            session()->forget('coupon');
            session()->forget('coupon_code');
        }

        return view('checkout.success', compact('order'));
    }
}
