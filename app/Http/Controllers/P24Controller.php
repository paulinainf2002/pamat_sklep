<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class P24Controller extends Controller
{
    public function redirect($orderId)
    {
        $order = Order::findOrFail($orderId);

        // ====== DANE P24 (TYLKO STĄD) ======
        $merchantId = (int) config('services.p24.merchant_id'); // 370681
        $posId      = (int) config('services.p24.pos_id');      // 6440827
        $apiKey     = trim(config('services.p24.api_key'));     // KLUCZ API
        $crc        = trim(config('services.p24.crc'));         // CRC

        // ====== KWOTA W GROSZACH ======
        $amount = (int) round($order->total * 100);

        // ====== SIGN ======
        $sign = hash(
            'sha384',
            json_encode([
                'sessionId'  => $order->order_number,
                'merchantId' => $merchantId,
                'amount'     => $amount,
                'currency'   => 'PLN',
                'crc'        => $crc,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
        Log::info('P24 AUTH DEBUG', [
            'basic_auth_login' => $posId,
            'merchantId' => $merchantId,
            'posId' => $posId,
            'apiKey_len' => strlen($apiKey),
        ]);

        // ====== REQUEST ======
        $response = Http::withBasicAuth($merchantId, $apiKey)
            ->post('https://secure.przelewy24.pl/api/v1/transaction/register', [
                'merchantId' => $merchantId,
                'posId'      => $posId,
                'sessionId'  => $order->order_number,
                'amount'     => $amount,
                'currency'   => 'PLN',
                'description'=> 'Zamówienie PaMat ' . $order->order_number,
                'email'      => $order->email,
                'country'    => 'PL',
                'language'   => 'pl',
                'urlReturn'  => route('p24.return', $order->order_number),
                'urlStatus'  => route('p24.status', $order->order_number),
                'sign'       => $sign,
            ]);

        if (! $response->successful()) {
            Log::error('P24 ERROR', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            abort(500, 'Błąd połączenia z Przelewy24');
        }

        return redirect($response->json()['data']['redirectUrl']);
    }

    public function return($orderNumber)
    {
        return redirect()->route('checkout.success', $orderNumber);
    }

    public function status(Request $request, $orderNumber)
    {
        // webhook – na razie nie ruszamy
        return response()->json(['status' => 'OK']);
    }
}
