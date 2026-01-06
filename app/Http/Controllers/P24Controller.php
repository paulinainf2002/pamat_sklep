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

        // ====== DANE P24 ======
        $merchantId = (int) config('services.p24.merchant_id'); // 370681
        $posId      = (int) config('services.p24.pos_id');      // 370681
        $apiKey     = trim((string) config('services.p24.api_key'));
        $crc        = trim((string) config('services.p24.crc'));

        if (!$merchantId || !$posId || $apiKey === '' || $crc === '') {
            Log::error('P24 CONFIG ERROR', [
                'merchantId' => $merchantId,
                'posId'      => $posId,
                'apiKey_len' => strlen($apiKey),
                'crc_len'    => strlen($crc),
            ]);
            abort(500, 'Błąd konfiguracji P24 (brak danych w ENV).');
        }

        // ====== KWOTA W GROSZACH ======
        $amount = (int) round($order->total * 100);

        // ====== SIGN ======
        $payloadForSign = [
            'sessionId'  => $order->order_number,
            'merchantId' => $merchantId,
            'amount'     => $amount,
            'currency'   => 'PLN',
            'crc'        => $crc,
        ];

        $sign = hash(
            'sha384',
            json_encode($payloadForSign, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        // ====== DEBUG ======
        Log::info('P24 AUTH DEBUG', [
            'basic_auth_login_should_be_posId' => $posId,
            'merchantId' => $merchantId,
            'posId'      => $posId,
            'apiKey_len' => strlen($apiKey),
            'amount'     => $amount,
            'sessionId'  => $order->order_number,
        ]);

        // ====== REQUEST ======
        // !!! Najważniejsze: BasicAuth = POS_ID + API_KEY (nie merchantId)
        $response = Http::withBasicAuth((string) $posId, $apiKey)
            ->acceptJson()
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
                'status'     => $response->status(),
                'body'       => $response->body(),
                'merchantId' => $merchantId,
                'posId'      => $posId,
            ]);

            abort(500, 'Błąd połączenia z Przelewy24');
        }

        $json = $response->json();

        // P24 czasem zwraca struktury różnie; zabezpieczmy się
        $redirectUrl = $json['data']['redirectUrl'] ?? null;

        if (! $redirectUrl) {
            Log::error('P24 RESPONSE ERROR', ['json' => $json]);
            abort(500, 'Błędna odpowiedź z Przelewy24 (brak redirectUrl).');
        }

        return redirect($redirectUrl);
    }

    public function return($orderNumber)
    {
        return redirect()->route('checkout.success', $orderNumber);
    }

    public function status(Request $request, $orderNumber)
    {
        // webhook – na razie zostawiamy
        return response()->json(['status' => 'OK']);
    }
}
