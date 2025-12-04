@extends('layout.app')


@section('content')


<div class="checkout-wrapper">

    <h1 class="checkout-title">Podsumowanie zamówienia</h1>

    <div class="checkout-container">

        <div class="checkout-products">
            @foreach ($cart as $item)
                <div class="checkout-item">
                    <img src="{{ asset('storage/' . $item['image']) }}" alt="">

                    <div class="checkout-item-info">
                        <h3>{{ $item['name'] }}</h3>
                        <p>Ilość: {{ $item['quantity'] }}</p>
                        <p>Cena: {{ number_format($item['price'], 2) }} zł</p>
                    </div>

                    <div class="checkout-item-total">
                        {{ number_format($item['price'] * $item['quantity'], 2) }} zł
                    </div>
                </div>
            @endforeach
        </div>

        <div class="checkout-summary-box">
            <h2>Dane klienta</h2>

            <form action="{{ route('checkout.place') }}" method="POST">
                @csrf

                <input type="text" name="name" placeholder="Imię i nazwisko" required>
                <input type="email" name="email" placeholder="Adres e-mail" required>
                <input type="text" name="phone" placeholder="Telefon" required>
                <input type="text" name="address" placeholder="Adres" required>
                <input type="text" name="postal_code" placeholder="Kod pocztowy" required>
                <input type="text" name="city" placeholder="Miasto" required>

                <h3>Łącznie do zapłaty:
                    <strong>{{ number_format($total, 2) }} zł</strong>
                </h3>

                <button class="checkout-button">Złóż zamówienie</button>
            </form>
        </div>

    </div>
</div>
@endsection
