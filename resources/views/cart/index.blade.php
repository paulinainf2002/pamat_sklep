@extends('layout.app')

@section('content')
<div class="cart-container">

    <!-- <h1 class="cart-title">Twój koszyk</h1> -->

    @if (session('success'))
        <div class="cart-alert">
            {{ session('success') }}
        </div>
    @endif

    @php $total = 0; @endphp

    @if(empty($cart))

        <div class="empty-cart">
            <img src="{{ asset('images/cart-big.svg') }}" alt="" class="empty-cart-img">

            <h2>Twój koszyk jest pusty</h2>

            <p class="empty-cart-text">
                Na razie jest tu jeszcze pusto.<br>
                Wybierz kategorię i znajdź coś dla siebie.
            </p>

            <div class="empty-cart-buttons">
                <a href="{{ route('categories.show', 1) }}" class="empty-cart-btn">Herbaty</a>
                <a href="{{ route('categories.show', 2) }}" class="empty-cart-btn">Kawy</a>
                <a href="{{ route('categories.show', 6) }}" class="empty-cart-btn">Zestawy</a>
            </div>
        </div>

    @else

        <div class="cart-list">

        @foreach ($cart as $index => $item)

            @php
                $total += $item['price'];

                if ($item['type'] === 'set') {
                    $unitPriceLabel = number_format($item['price_per_unit'], 2) . ' zł / zestaw';
                } else {
                    $price100g = $item['price_per_unit'] / ($item['weight'] / 100);
                    $unitPriceLabel = number_format($price100g, 2) . ' zł / 100g';
                }
            @endphp

            <div class="cart-item">

                <div class="cart-thumb">
                    <img src="{{ Storage::url($item['image']) }}" alt="">
                </div>

                <div class="cart-info">
                    <div class="cart-name">{{ $item['name'] }}</div>
                    <div class="cart-unit">{{ $unitPriceLabel }}</div>
                </div>

                <div class="cart-qty">
                    <!-- <form action="{{ route('cart.decrease', $index) }}" method="POST">
                        @csrf
                        <button class="qty-btn minus">−</button>
                    </form> -->

                    <div class="qty-value">
                        @if($item['type'] === 'set')
                            {{ $item['quantity'] }} szt.
                        @else
                            {{ $item['quantity'] }} × {{ $item['weight'] }} g
                        @endif
                    </div>

                    <!-- <form action="{{ route('cart.increase', $index) }}" method="POST">
                        @csrf
                        <button class="qty-btn plus">+</button>
                    </form> -->
                </div>

                <div class="cart-total">
                    {{ number_format($item['price'], 2) }} zł
                </div>

                <form action="{{ route('cart.remove', $index) }}" method="POST">
                    @csrf
                    <button class="remove-btn">Usuń</button>
                </form>

            </div>

        @endforeach

        </div>

        <div class="cart-summary">
            Razem: <strong>{{ number_format($total, 2) }} zł</strong>
        </div>
<!--
<div class="cart-actions">
    <a href="{{ route('delivery.index') }}" class="btn-green">Przejdź do dostawy</a>
</div> -->
        <form action="{{ route('cart.clear') }}" method="POST" class="cart-clear">
            @csrf
            <button class="btn-clear-all">Wyczyść koszyk</button>
        </form>
<!-- <hr style="margin: 3rem 0; border-color:#e5e7eb;">

<h2 style="color:#3E4E20; margin-bottom:1.5rem;">Dostawa i płatność</h2>


{{-- KOD RABATOWY --}}
<div class="checkout-box">
    <form action="{{ route('checkout.coupon') }}" method="POST" class="coupon-form">
        @csrf

        <label>Kod rabatowy:</label>

        <div class="coupon-row">
            <input type="text" name="coupon" placeholder="Wpisz kod..." value="{{ session('coupon_code') }}">
            <button class="btn-apply">Zastosuj</button>
        </div>

        @if (session('coupon'))
            <p class="coupon-success">Zastosowano kod: <strong>{{ session('coupon_code') }}</strong></p>
        @endif
    </form>
</div>



{{-- DOSTAWA --}}
<div class="checkout-box">
    <h3>Wybierz metodę dostawy</h3>

    <form action="{{ route('checkout.shipping') }}" method="POST">
        @csrf

        <label class="radio-line">
            <input type="radio" name="shipping" value="inpost"
                   {{ session('shipping','inpost')=='inpost' ? 'checked' : '' }}>
            Paczkomat InPost — 10.99 zł
        </label>

        <label class="radio-line">
            <input type="radio" name="shipping" value="kurier"
                   {{ session('shipping')=='kurier' ? 'checked' : '' }}>
            Kurier — 12.99 zł
        </label>

        <label class="radio-line">
            <input type="radio" name="shipping" value="odbior"
                   {{ session('shipping')=='odbior' ? 'checked' : '' }}>
            Odbiór osobisty — 0 zł
        </label>

        <button class="btn-save">Zapisz</button>
    </form>
</div> -->
<hr style="margin: 3rem 0; border-color:#e5e7eb;">

<h2 style="color:#3E4E20; margin-bottom:1.5rem;">Dostawa i płatność</h2>

<div class="checkout-flex">

    {{-- LEWA KOLUMNA – DOSTAWA --}}
    <div class="checkout-box">
        <h3>Dostawa</h3>

        <label class="radio-line">
            <input type="radio" name="shipping" value="inpost"
                {{ session('shipping','inpost')=='inpost' ? 'checked' : '' }}>
            Paczkomat InPost — 10.99 zł
        </label>

        <label class="radio-line">
            <input type="radio" name="shipping" value="kurier"
                {{ session('shipping')=='kurier' ? 'checked' : '' }}>
            Kurier — 12.99 zł
        </label>

        <!-- <label class="radio-line">
            <input type="radio" name="shipping" value="odbior"
                {{ session('shipping')=='odbior' ? 'checked' : '' }}>
            Odbiór osobisty — 0 zł
        </label> -->
    </div>


    {{-- PRAWA KOLUMNA – PŁATNOŚĆ --}}
    <div class="checkout-box">
        <h3>Płatność</h3>

        <label class="radio-line">
            <input type="radio" name="payment" value="p24"
                {{ session('payment','p24')=='p24' ? 'checked' : '' }}>
            Przelewy24 (BLIK, przelew, karta)
        </label>

        <label class="radio-line">
            <input type="radio" name="payment" value="przelew"
                {{ session('payment')=='przelew' ? 'checked' : '' }}>
            Przelew tradycyjny
        </label>

        <!-- <label class="radio-line">
            <input type="radio" name="payment" value="pobranie"
                {{ session('payment')=='pobranie' ? 'checked' : '' }}>
            Pobranie (+ 5 zł)
        </label> -->
    </div>

</div>


{{-- KOD RABATOWY --}}
<div class="checkout-box" style="margin-top: 2rem;">
    <h3>Kod rabatowy</h3>

    <form action="{{ route('checkout.coupon') }}" method="POST" class="coupon-form">
        @csrf

        <div class="coupon-row">
            <input type="text" name="coupon" placeholder="np. RABAT10"
                   value="{{ session('coupon_code') }}">
            <button class="btn-apply">Zastosuj</button>
        </div>

        @if (session('coupon'))
            <p class="coupon-success">
                Zastosowano kod: <strong>{{ session('coupon_code') }}</strong>
                ({{ session('coupon') * 100 }}%)
            </p>
        @endif
    </form>
</div>
<div id="summaryBox" class="checkout-box" style="margin-top: 2rem;">
    <h3>Podsumowanie zamówienia</h3>

    <table class="summary-table">
        <tr>
            <td>Suma produktów:</td>
            <td id="sumProducts">0 zł</td>
        </tr>

        <tr>
            <td>Rabat (@if(session('coupon')){{ session('coupon') * 100 }}%@else 0%@endif):</td>
            <td id="sumDiscount">0 zł</td>
        </tr>

        <tr>
            <td>Dostawa:</td>
            <td id="sumShipping">0 zł</td>
        </tr>

        <!-- <tr>
            <td>Płatność:</td>
            <td id="sumPayment">0 zł</td>
        </tr> -->

        <tr class="summary-total-row">
            <td><strong>Razem do zapłaty:</strong></td>
            <td id="sumTotal"><strong>0 zł</strong></td>
        </tr>
    </table>
</div>



{{-- PRZYCISK DALEJ --}}
<div style="text-align:right; margin-top:2rem;">
    <a href="{{ route('checkout.summary') }}" class="checkout-next-btn">
        Przejdź do podsumowania zamówienia →
    </a>
</div>



<!-- {{-- PŁATNOŚCI --}}
<div class="checkout-box">
    <h3>Wybierz metodę płatności</h3>

    <form action="{{ route('checkout.payment') }}" method="POST">
        @csrf

        <label class="radio-line">
            <input type="radio" name="payment" value="p24"
                   {{ session('payment','p24')=='p24' ? 'checked' : '' }}>
            Przelewy24 (BLIK, przelew, karta)
        </label>

        <label class="radio-line">
            <input type="radio" name="payment" value="przelew"
                   {{ session('payment')=='przelew' ? 'checked' : '' }}>
            Przelew tradycyjny
        </label>

        <label class="radio-line">
            <input type="radio" name="payment" value="pobranie"
                   {{ session('payment')=='pobranie' ? 'checked' : '' }}>
            Pobranie (+ 5 zł)
        </label>

        <button class="btn-save">Zapisz</button>
    </form>
</div>



{{-- PODSUMOWANIE CHECKOUTU --}}
<div style="text-align:right; margin-top:2rem;">
    <a href="{{ route('checkout.summary') }}" class="checkout-next-btn">
        Przejdź do podsumowania zamówienia →
    </a>
</div> -->

    @endif
</div>
<script>
document.querySelectorAll('input[name="shipping"]').forEach(el => {
    el.addEventListener('change', () => {
        fetch("{{ route('checkout.shipping') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ shipping: el.value })
        });
    });
});

document.querySelectorAll('input[name="payment"]').forEach(el => {
    el.addEventListener('change', () => {
        fetch("{{ route('checkout.payment') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ payment: el.value })
        });
    });
});

// function calculateSummary() {
//     let cartTotal = {{ $total ?? 0 }}; // suma produktów z PHP
//     let coupon = {{ session('coupon', 0) }};
//     let shipping = 0;
//     let payment = 0;

//     // Pobieramy metodę dostawy z sesji
//     let shippingMethod = "{{ session('shipping', 'inpost') }}";

//     if (shippingMethod === 'inpost') shipping = 10.99;
//     if (shippingMethod === 'kurier') shipping = 12.99;
//     if (shippingMethod === 'odbior') shipping = 0;

//     // Pobieramy metodę płatności
//     let paymentMethod = "{{ session('payment', 'p24') }}";

//     if (paymentMethod === 'p24') payment = 0;
//     if (paymentMethod === 'przelew') payment = 0;
//     if (paymentMethod === 'pobranie') payment = 5.00;

//     // obliczenia
//     let discount = cartTotal * coupon;
//     let final = cartTotal - discount + shipping + payment;

//     // zaokrąglenia
//     discount = discount.toFixed(2);
//     final = final.toFixed(2);

//     // dynamiczne wpisywanie do HTML
//     document.getElementById('sumProducts').innerText = cartTotal.toFixed(2) + " zł";
//     document.getElementById('sumDiscount').innerText = "- " + discount + " zł";
//     document.getElementById('sumShipping').innerText = shipping.toFixed(2) + " zł";
//     document.getElementById('sumPayment').innerText = payment.toFixed(2) + " zł";
//     document.getElementById('sumTotal').innerHTML = "<strong>" + final + " zł</strong>";
// }

// // wywołanie na start
// calculateSummary();


// // ——————————————————————————————————————
// // AUTO-AKTUALIZACJA przy zmianie DOSTAWY
// // ——————————————————————————————————————
// document.querySelectorAll('input[name="shipping"]').forEach(el => {
//     el.addEventListener('change', () => {
//         fetch("{{ route('checkout.shipping') }}", {
//             method: "POST",
//             headers: {
//                 "X-CSRF-TOKEN": "{{ csrf_token() }}",
//                 "Content-Type": "application/json"
//             },
//             body: JSON.stringify({ shipping: el.value })
//         }).then(() => {
//             calculateSummary();
//         });
//     });
// });


// // ——————————————————————————————————————
// // AUTO-AKTUALIZACJA przy zmianie PŁATNOŚCI
// // ——————————————————————————————————————
// document.querySelectorAll('input[name="payment"]').forEach(el => {
//     el.addEventListener('change', () => {
//         fetch("{{ route('checkout.payment') }}", {
//             method: "POST",
//             headers: {
//                 "X-CSRF-TOKEN": "{{ csrf_token() }}",
//                 "Content-Type": "application/json"
//             },
//             body: JSON.stringify({ payment: el.value })
//         }).then(() => {
//             calculateSummary();
//         });
//     });
// });


// // ——————————————————————————————————————
// // AUTO-AKTUALIZACJA PO ZASTOSOWANIU KODU
// // ——————————————————————————————————————
// @if(session('coupon'))
//     setTimeout(calculateSummary, 400);
// @endif
</script>
<script>
function calculateSummary() {

    let cartTotal = {{ $total ?? 0 }};
    let coupon = {{ session('coupon', 0) }};

    // AKTUALNE wartości z zaznaczonych radio buttonów
    let shippingMethod = document.querySelector('input[name="shipping"]:checked').value;
    let paymentMethod  = document.querySelector('input[name="payment"]:checked').value;

    let shipping = 0;
    let payment = 0;

    // Dostawa
    if (shippingMethod === 'inpost') shipping = 10.99;
    if (shippingMethod === 'kurier') shipping = 12.99;
    if (shippingMethod === 'odbior') shipping = 0;

    // Płatność
    if (paymentMethod === 'p24') payment = 0;
    if (paymentMethod === 'przelew') payment = 0;
    if (paymentMethod === 'pobranie') payment = 5.00;

    let discount = cartTotal * coupon;
    let final = cartTotal - discount + shipping + payment;

    // Zaokrąglenia i wyświetlenie
    document.getElementById('sumProducts').innerText = cartTotal.toFixed(2) + " zł";
    document.getElementById('sumDiscount').innerText = "- " + discount.toFixed(2) + " zł";
    document.getElementById('sumShipping').innerText = shipping.toFixed(2) + " zł";
    // document.getElementById('sumPayment').innerText = payment.toFixed(2) + " zł";
    document.getElementById('sumTotal').innerHTML = "<strong>" + final.toFixed(2) + " zł</strong>";
}



// wywołanie na start
calculateSummary();


// ——————————————————————————————————————
// AUTO-AKTUALIZACJA przy zmianie DOSTAWY
// ——————————————————————————————————————
document.querySelectorAll('input[name="shipping"]').forEach(el => {
    el.addEventListener('change', () => {
        fetch("{{ route('checkout.shipping') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ shipping: el.value })
        });

        calculateSummary(); // <— NATYCHMIASTOWE PRZELICZENIE
    });
});

document.querySelectorAll('input[name="payment"]').forEach(el => {
    el.addEventListener('change', () => {
        fetch("{{ route('checkout.payment') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ payment: el.value })
        });

        calculateSummary(); // <— NATYCHMIASTOWE PRZELICZENIE
    });
});



// ——————————————————————————————————————
// AUTO-AKTUALIZACJA PO ZASTOSOWANIU KODU
// ——————————————————————————————————————
@if(session('coupon'))
    setTimeout(calculateSummary, 400);
@endif
function formatMoney(value) {
    return value.toFixed(2) + " zł";
}
document.getElementById('sumShipping').innerText = formatMoney(shipping);
// document.getElementById('sumPayment').innerText = formatMoney(payment);
document.getElementById('sumTotal').innerHTML = "<strong>" + formatMoney(final) + "</strong>";

</script>

@endsection
