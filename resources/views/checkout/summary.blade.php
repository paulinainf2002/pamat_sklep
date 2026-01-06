@extends('layout.app')

@section('content')

@php
    use Illuminate\Support\Facades\Storage;
@endphp
@php
$delivery_point = old('delivery_point', request('delivery_point'));
@endphp

<div class="summary-container">

    <h1 class="summary-title">Podsumowanie zamówienia</h1>

    {{-- PRODUKTY --}}
    <div class="summary-section">
        <h2 class="summary-heading">Produkty</h2>

        @foreach ($cart as $item)
            <div class="summary-product">
                <div class="summary-product-left">
                    <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}" class="summary-product-thumb">

                    <div class="summary-product-text">
                        <div class="summary-product-name">{{ $item['name'] }}</div>
                        <div class="summary-product-meta">
                            @if ($item['type'] === 'set')
                                {{ $item['quantity'] }} × zestaw
                            @else
                                {{ $item['quantity'] }} × {{ $item['weight'] }} g
                            @endif
                        </div>
                    </div>
                </div>

                <div class="summary-product-price">
                    {{ number_format($item['price'], 2) }} zł
                </div>
            </div>
        @endforeach
    </div>

    <hr class="summary-divider">

    {{-- DANE KLIENTA --}}
    <div class="summary-section">
        <h2 class="summary-heading">Dane klienta</h2>

        <div class="summary-row">
            <span>Imię i nazwisko:</span>
            <span>{{ session('checkout_name') }}</span>
        </div>
        <div class="summary-row">
            <span>Email:</span>
            <span>{{ session('checkout_email') }}</span>
        </div>
        <div class="summary-row">
            <span>Telefon:</span>
            <span>{{ session('checkout_phone') }}</span>
        </div>
    </div>

    <hr class="summary-divider">

    {{-- DOSTAWA --}}
    <div class="summary-section">
        <h2 class="summary-heading">Dostawa</h2>

        @if (session('checkout_delivery_method') === 'inpost')
            <div class="summary-row">
                <span>Metoda:</span>
                <span>Paczkomat InPost</span>
            </div>
            <div class="summary-row">
                <span>Paczkomat:</span>
                <span>{{ $delivery_point }}</span>
            </div>
        @else
            <div class="summary-row">
                <span>Metoda:</span>
                <span>Kurier</span>
            </div>
            <div class="summary-row">
                <span>Adres:</span>
                <span>{{ session('checkout_address') }}</span>
            </div>
            <div class="summary-row">
                <span>Kod pocztowy:</span>
                <span>{{ session('checkout_postal_code') }}</span>
            </div>
            <div class="summary-row">
                <span>Miasto:</span>
                <span>{{ session('checkout_city') }}</span>
            </div>
        @endif
    </div>

    <hr class="summary-divider">

    {{-- PŁATNOŚĆ --}}
    <div class="summary-section">
        <h2 class="summary-heading">Płatność</h2>
        <div class="summary-row">
            <span>Metoda:</span>
            <span>
                @if (session('checkout_payment_method') === 'p24')
                    Przelewy24
                @else
                    Przelew tradycyjny
                @endif
            </span>
        </div>
    </div>

    <hr class="summary-divider">

    {{-- SUMA --}}
    <div class="summary-section">
        <h2 class="summary-heading">Podsumowanie płatności</h2>

        <div class="summary-row">
            <span>Suma produktów:</span>
            <span>{{ number_format($productsTotal, 2) }} zł</span>
        </div>

        <div class="summary-row">
            <span>Rabat:</span>
            <span>- {{ number_format($discount, 2) }} zł
                @if(session('coupon'))
                    ({{ session('coupon') * 100 }}%)
                @endif
            </span>
        </div>

        <div class="summary-row">
            <span>Dostawa:</span>
            <span>{{ number_format($shipping, 2) }} zł</span>
        </div>

        <div class="summary-total">
            Razem do zapłaty: {{ number_format($final, 2) }} zł
        </div>
    </div>

    {{-- FORMULARZ WYSYŁKI --}}
    <form action="{{ route('checkout.placeOrder') }}" method="POST" class="summary-form">
        @csrf

        {{-- Dane klienta --}}
        <input type="hidden" name="name" value="{{ session('checkout_name') }}">
        <input type="hidden" name="email" value="{{ session('checkout_email') }}">
        <input type="hidden" name="phone" value="{{ session('checkout_phone') }}">

        {{-- Dostawa --}}
        <input type="hidden" name="delivery_method" value="{{ session('checkout_delivery_method') }}">

        @if (session('checkout_delivery_method') === 'inpost')
            <input type="hidden" name="delivery_point" value="{{ session('inpost_point') }}">
        @else
            <input type="hidden" name="address" value="{{ session('checkout_address') }}">
            <input type="hidden" name="postal_code" value="{{ session('checkout_postal_code') }}">
            <input type="hidden" name="city" value="{{ session('checkout_city') }}">
        @endif

        {{-- Płatność --}}
        <input type="hidden" name="payment_method" value="{{ session('checkout_payment_method') }}">
        <input type="hidden" name="final_amount" value="{{ $final }}">

        <button type="submit" class="summary-submit-btn">
            Złóż zamówienie →
        </button>
    </form>

</div>

@endsection
