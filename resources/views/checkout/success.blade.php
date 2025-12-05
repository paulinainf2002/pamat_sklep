@extends('layout.app')

@section('content')
<div class="checkout-wrapper" style="text-align:center; max-width:650px; margin:4rem auto;">

    <h1 class="checkout-title" style="font-size:32px; color:#3E4E20; font-weight:600;">
        Dziękujemy za zamówienie!
    </h1>

    <p style="margin-top:1rem; font-size:18px;">
        Twój numer zamówienia:
    </p>

    <h2 style="color:#D4A24C; margin-top:5px; font-size:26px; font-weight:700;">
        {{ $order->order_number }}
    </h2>

    <hr style="margin:2rem 0; border-color:#e5e7eb;">


    {{-- Jeśli wybrano przelew tradycyjny --}}
    @if ($order->payment_method === 'transfer')

        <h3 style="color:#3E4E20; margin-bottom:1rem; font-size:22px;">
            Instrukcje do przelewu tradycyjnego
        </h3>

        <p style="font-size:17px; line-height:1.6; margin-bottom:1.5rem;">
            W związku z wyborem <strong>przelewu tradycyjnego</strong> prosimy o wysłanie kwoty:
        </p>

        <div style="font-size:22px; font-weight:700; color:#3E4E20; margin-bottom:1.5rem;">
            {{ number_format($order->total, 2) }} zł
        </div>

        <p style="font-size:17px; line-height:1.6;">
            na numer konta bankowego:<br>
            <strong style="font-size:20px; color:#3E4E20;">
                71 1050 1113 1000 0097 3547 1105
            </strong>
        </p>

        <p style="font-size:17px; margin-top:1rem;">
            z tytułem przelewu:
        </p>

        <div style="font-size:20px; font-weight:600; color:#D4A24C; margin-bottom:2rem;">
            {{ $order->order_number }}
        </div>

        <p style="font-size:16px; line-height:1.6;">
            Po zaksięgowaniu wpłaty otrzymają Państwo wiadomość e-mail
            na adres <strong>{{ $order->email }}</strong> z potwierdzeniem realizacji zamówienia.
        </p>

    @else
        {{-- WERSJA DLA P24 NA PRZYSZŁOŚĆ --}}
        <p style="font-size:17px; margin-top:1rem;">
            Dziękujemy za skorzystanie z płatności online.
        </p>
        <p style="font-size:17px;">
            Potwierdzenie zostanie wysłane na <strong>{{ $order->email }}</strong>.
        </p>
    @endif


    <hr style="margin:3rem 0; border-color:#e5e7eb;">

    <a href="{{ route('home') }}"
       style="display:inline-block; padding:12px 24px; background:#3E4E20; color:white;
              border-radius:6px; text-decoration:none; font-size:16px;">
        Powrót do sklepu
    </a>

</div>
@endsection
