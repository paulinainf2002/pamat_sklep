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
        // Trzymamy jako STRING (bez ryzyka zmian formatu)
        $merchantId = (string) config('services.p24.merchant_id'); // np. "370681"
        $posId      = (string) config('services.p24.pos_id');      // np. "6440827"
        $apiKey     = trim((string) config('services.p24.api_key'));
        $crc        = trim((string) config('services.p24.crc'));

        // ====== SZYBKA WALIDACJA KONFIGU ======
        if ($merchantId === '' || $posId === '' || $apiKey === '' || $crc === '') {
            Log::error('P24 CONFIG MISSING', [
                'merchantId_present' => $merchantId !== '',
                'posId_present'      => $posId !== '',
                'apiKey_len'         => strlen($apiKey),
                'crc_len'            => strlen($crc),
            ]);

            abort(500, 'Brakuje konfiguracji Przelewy24 (sprawdź ENV).');
        }

        // ====== KWOTA W GROSZACH ======
        $amount = (int) round(((float) $order->total) * 100);

        // ====== SIGN (sha384 z JSON) ======
        $sign = hash(
            'sha384',
            json_encode([
                'sessionId'  => $order->order_number,
                'merchantId' => (int) $merchantId,
                'amount'     => $amount,
                'currency'   => 'PLN',
                'crc'        => $crc,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        // ====== HTTP CLIENT ======
        // BasicAuth: login = merchantId, hasło = apiKey
        $http = Http::withBasicAuth($merchantId, $apiKey)
            ->acceptJson()
            ->asJson();

        // ====== DEBUG: IP SERWERA (częsty powód 401 w chmurze) ======
        try {
            $ip = Http::timeout(5)->get('https://api.ipify.org?format=json')->json();
            Log::info('P24 SERVER PUBLIC IP', ['ipify' => $ip]);
        } catch (\Throwable $e) {
            Log::warning('P24 SERVER PUBLIC IP FAIL', ['msg' => $e->getMessage()]);
        }

        // ====== DEBUG: testAccess (czy autoryzacja działa w ogóle) ======
        try {
            $test = $http->get('https://secure.przelewy24.pl/api/v1/testAccess');
            Log::info('P24 TEST ACCESS', [
                'status' => $test->status(),
                'body'   => $test->body(),
                'basic_auth_login' => $merchantId,
                'merchantId' => $merchantId,
                'posId'      => $posId,
                'apiKey_len' => strlen($apiKey),
            ]);
        } catch (\Throwable $e) {
            Log::error('P24 TEST ACCESS EXCEPTION', ['msg' => $e->getMessage()]);
        }

        // ====== DEBUG: autoryzacja (poprawnie logowana) ======
        Log::info('P24 AUTH DEBUG', [
            'basic_auth_login' => $merchantId, // <-- TERAZ ZGODNE Z withBasicAuth
            'merchantId' => $merchantId,
            'posId' => $posId,
            'apiKey_len' => strlen($apiKey),
            'amount' => $amount,
            'sessionId' => $order->order_number,
        ]);

        // ====== REQUEST: REGISTER ======
        $response = $http->post(
            'https://secure.przelewy24.pl/api/v1/transaction/register',
            [
                'merchantId'  => (int) $merchantId,
                'posId'       => (int) $posId,
                'sessionId'   => $order->order_number,
                'amount'      => $amount,
                'currency'    => 'PLN',
                'description' => 'Zamówienie PaMat ' . $order->order_number,
                'email'       => $order->email,
                'country'     => 'PL',
                'language'    => 'pl',
                'urlReturn'   => route('p24.return', $order->order_number),
                'urlStatus'   => route('p24.status', $order->order_number),
                'sign'        => $sign,
            ]
        );

        if (! $response->successful()) {
            Log::error('P24 ERROR', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'merchantId' => $merchantId,
                'posId' => $posId,
            ]);

            abort(500, 'Błąd połączenia z Przelewy24');
        }

        $json = $response->json();

        if (!isset($json['data']['redirectUrl'])) {
            Log::error('P24 ERROR: missing redirectUrl', [
                'json' => $json,
            ]);

            abort(500, 'P24 nie zwróciło redirectUrl.');
        }

        return redirect($json['data']['redirectUrl']);
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
