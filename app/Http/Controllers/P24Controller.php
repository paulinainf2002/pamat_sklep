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
        $merchantId = (int) config('services.p24.merchant_id'); // np. 370681 (ID konta)
        $posId      = (int) config('services.p24.pos_id');      // np. 6440827 (User do REST)
        $apiKey     = trim((string) config('services.p24.api_key')); // REST API key (secretId)
        $crc        = trim((string) config('services.p24.crc'));     // CRC

        if (!$merchantId || !$posId || !$apiKey || !$crc) {
            Log::error('P24 CONFIG ERROR', [
                'merchantId' => $merchantId,
                'posId'      => $posId,
                'apiKey_len' => strlen($apiKey),
                'crc_len'    => strlen($crc),
            ]);
            abort(500, 'Brak konfiguracji Przelewy24 (merchantId/posId/apiKey/crc).');
        }

        // ====== KWOTA W GROSZACH ======
        $amount = (int) round(((float) $order->total) * 100);

        // ====== SIGN (SHA384 z JSON) ======
        $signPayload = [
            'sessionId'  => (string) $order->order_number,
            'merchantId' => $merchantId,
            'amount'     => $amount,
            'currency'   => 'PLN',
            'crc'        => $crc,
        ];

        $sign = hash(
            'sha384',
            json_encode($signPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        // DEBUG (bez sekretów)
        Log::info('P24 AUTH DEBUG', [
            'basic_auth_login' => $posId,         // <-- TO MUSI BYĆ posId
            'merchantId'       => $merchantId,
            'posId'            => $posId,
            'apiKey_len'       => strlen($apiKey),
            'amount'           => $amount,
            'sessionId'        => $order->order_number,
        ]);

        // ====== REQUEST ======
        // BasicAuth: login = posId, password = apiKey/secretId  :contentReference[oaicite:1]{index=1}
        $response = Http::withBasicAuth($posId, $apiKey)
            ->acceptJson()
            ->asJson()
            ->post('https://secure.przelewy24.pl/api/v1/transaction/register', [
                'merchantId'  => $merchantId,
                'posId'       => $posId,
                'sessionId'   => (string) $order->order_number,
                'amount'      => $amount,
                'currency'    => 'PLN',
                'description' => 'Zamówienie PaMat ' . $order->order_number,
                'email'       => (string) $order->email,
                'country'     => 'PL',
                'language'    => 'pl',
                'urlReturn'   => route('p24.return', $order->order_number),
                'urlStatus'   => route('p24.status', $order->order_number),
                'sign'        => $sign,
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

        // zwykle: data.redirectUrl
        $redirectUrl = $json['data']['redirectUrl'] ?? null;

        if (! $redirectUrl) {
            Log::error('P24 ERROR - missing redirectUrl', ['json' => $json]);
            abort(500, 'P24 nie zwróciło adresu przekierowania.');
        }

        return redirect($redirectUrl);
    }

    public function return($orderNumber)
    {
        return redirect()->route('checkout.success', $orderNumber);
    }

    public function status(Request $request, $orderNumber)
    {
        // webhook/status – ogarniemy później, na razie OK
        return response()->json(['status' => 'OK']);
    }
}
