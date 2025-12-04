@extends('layout.app')

@section('content')
<div class="cart-container">

    <h1 class="cart-title">Podsumowanie zamówienia</h1>

    {{-- PRODUKTY --}}
    <div class="checkout-box">
        <h2>Twoje produkty</h2>

        @foreach($cart as $item)
            <div class="cart-item">
                <div class="cart-thumb">
                    <img src="{{ Storage::url($item['image']) }}" alt="">
                </div>

                <div class="cart-info">
                    <div class="cart-name">{{ $item['name'] }}</div>

                    <div class="cart-unit">
                        @if($item['type'] === 'set')
                            {{ $item['quantity'] }} × zestaw
                        @else
                            {{ $item['quantity'] }} × {{ $item['weight'] }} g
                        @endif
                    </div>
                </div>

                <div class="cart-total">
                    {{ number_format($item['price'], 2) }} zł
                </div>
            </div>
        @endforeach

        <div class="cart-summary" style="margin-top:1rem;">
            Suma produktów: <strong>{{ number_format($total, 2) }} zł</strong>
        </div>
    </div>

    {{-- DANE KLIENTA --}}
    <div class="checkout-box" style="margin-top:2rem;">
        <h2>Dane klienta</h2>

        <p><strong>Imię i nazwisko:</strong> {{ session('checkout_name') }}</p>
        <p><strong>E-mail:</strong> {{ session('checkout_email') }}</p>
        <p><strong>Telefon:</strong> {{ session('checkout_phone') }}</p>
    </div>

    {{-- DOSTAWA --}}
    @php
        $deliveryMethod = session('checkout_delivery_method', 'inpost');
        $paymentMethod  = session('checkout_payment_method', 'p24');
        $shippingPrice  = $deliveryMethod === 'inpost' ? 11.99 : 14.99;
        $finalTotal     = $total + $shippingPrice;
    @endphp

    <div class="checkout-box" style="margin-top:2rem;">
        <h2>Dostawa</h2>

        @if($deliveryMethod === 'inpost')
            <p><strong>Metoda:</strong> Paczkomat InPost</p>
            <p><strong>Punkt:</strong> {{ session('inpost_point') ?: 'Nie wybrano' }}</p>
        @else
            <p><strong>Metoda:</strong> Kurier</p>
            <p>
                <strong>Adres:</strong>
                {{ session('checkout_address') }},
                {{ session('checkout_postal_code') }}
                {{ session('checkout_city') }}
            </p>
        @endif

        <p><strong>Koszt dostawy:</strong> {{ number_format($shippingPrice, 2) }} zł</p>
    </div>

    {{-- PŁATNOŚĆ --}}
    <div class="checkout-box" style="margin-top:2rem;">
        <h2>Płatność</h2>

        <p>
            <strong>Metoda:</strong>
            @if($paymentMethod === 'p24')
                Przelewy24
            @else
                Przelew tradycyjny
            @endif
        </p>

        <p class="cart-summary">
            Razem do zapłaty:
            <strong>{{ number_format($finalTotal, 2) }} zł</strong>
        </p>
    </div>

    {{-- ZŁÓŻ ZAMÓWIENIE --}}
    <div style="text-align:right; margin-top:2rem;">
        <form action="{{ route('checkout.placeOrder') }}" method="POST">
    @csrf
    <button type="submit" class="checkout-next-btn">
        Złóż zamówienie →
    </button>
</form>

    </div>

</div>
@endsection
