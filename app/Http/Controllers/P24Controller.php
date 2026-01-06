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

        // ====== DANE P24 (z config/services.php -> services.p24.*) ======
        $merchantId = (int) config('services.p24.merchant_id'); // ID sprzedawcy (u Ciebie: 370681)
        $posId      = (int) config('services.p24.pos_id');      // zwykle POS = merchant (jeśli nie masz osobnego POS)
        $apiKey     = trim((string) config('services.p24.api_key'));
        $crc        = trim((string) config('services.p24.crc'));

        if (!$merchantId || !$posId || $apiKey === '' || $crc === '') {
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

        // ====== SIGN (sha384 z JSON) ======
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

        Log::info('P24 REGISTER DEBUG', [
            'basic_auth_login' => $merchantId,
            'merchantId'       => $merchantId,
            'posId'            => $posId,
            'apiKey_len'       => strlen($apiKey),
            'amount'           => $amount,
            'sessionId'        => $order->order_number,
        ]);

        // ====== REGISTER ======
        $response = Http::withBasicAuth($merchantId, $apiKey)
            ->acceptJson()
            ->asJson()
            ->post('https://secure.przelewy24.pl/api/v1/transaction/register', [
                'merchantId'  => $merchantId,
                'posId'       => $posId,
                'sessionId'   => (string) $order->order_number,
                'amount'      => $amount,
                'currency'    => 'PLN',
                'description' => 'Zamówienie PaMat ' . $order->order_number,
                'email'       => $order->email,
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

        // W API v1 często dostajesz token, a nie redirectUrl.
        $token = data_get($response->json(), 'data.token');

        if (!$token) {
            Log::error('P24 ERROR - missing token', [
                'json' => $response->json(),
            ]);
            abort(500, 'Przelewy24 nie zwróciło tokenu transakcji.');
        }

        // Link do płatności:
        $payUrl = 'https://secure.przelewy24.pl/trnRequest/' . $token;

        return redirect()->away($payUrl);
    }

    public function return($orderNumber)
    {
        // Klient wraca z P24 – docelowo i tak warto sprawdzać status,
        // ale na start przekieruj na "success".
        return redirect()->route('checkout.success', $orderNumber);
    }

    public function status(Request $request, $orderNumber)
    {
        // webhook/status – zostawiamy na później (verify)
        return response()->json(['status' => 'OK']);
    }
}
