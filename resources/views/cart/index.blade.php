@extends('layout.app')

@section('content')
<div class="cart-container">

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

        {{-- LISTA PRODUKTÓW --}}
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
                        <div class="qty-value">
                            @if($item['type'] === 'set')
                                {{ $item['quantity'] }} szt.
                            @else
                                {{ $item['quantity'] }} × {{ $item['weight'] }} g
                            @endif
                        </div>
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

        <form action="{{ route('cart.clear') }}" method="POST" class="cart-clear">
            @csrf
            <button class="btn-clear-all">Wyczyść koszyk</button>
        </form>

        {{-- FORMULARZ CHECKOUTU – WSZYSTKO LECI POSTEM DO /checkout/summary --}}
        <!-- <form action="{{ route('checkout.summary') }}" method="POST" style="margin-top: 3rem;">
            @csrf -->

            {{-- DANE KLIENTA --}}
            <hr style="margin: 3rem 0; border-color:#e5e7eb;">

            <h2 style="color:#3E4E20; margin-bottom:1.5rem;">Dane klienta</h2>

            <div class="checkout-box">
                <div class="checkout-flex-vertical">

                    <div class="checkout-field">
                        <label>Imię i nazwisko</label></br>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="checkout-input"
                            placeholder="Jan Kowalski"
                            value="{{ old('name', session('checkout_name')) }}"
                            required
                        >
                    </div>

                    <div class="checkout-field">
                        <label>Adres e-mail</label></br>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="checkout-input"
                            placeholder="jan.kowalski@example.com"
                            value="{{ old('email', session('checkout_email')) }}"
                            required
                        >
                    </div>

                    <div class="checkout-field">
                        <label>Telefon</label></br>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            class="checkout-input"
                            placeholder="123 456 789"
                            value="{{ old('phone', session('checkout_phone')) }}"
                            required
                        >
                    </div>

                </div>
            </div>

            {{-- DOSTAWA I PŁATNOŚĆ --}}
            <hr style="margin: 3rem 0; border-color:#e5e7eb;">

            <h2 style="color:#3E4E20; margin-bottom:1.5rem;">Dostawa i płatność</h2>

            <div class="checkout-flex">

                {{-- LEWA KOLUMNA – DOSTAWA --}}
                <div class="checkout-box">
                    <h3>Dostawa</h3>

                    @php
                        $deliveryMethod = session('checkout_delivery_method', 'inpost');
                        $paymentMethod  = session('checkout_payment_method', 'p24');
                    @endphp

                    <label class="radio-line">
                        <input type="radio" name="delivery_method" value="inpost"
                            {{ old('delivery_method', $deliveryMethod) === 'inpost' ? 'checked' : '' }}>
                        Paczkomat InPost — 11.99 zł
                    </label>

                    <label class="radio-line">
                        <input type="radio" name="delivery_method" value="kurier"
                            {{ old('delivery_method', $deliveryMethod) === 'kurier' ? 'checked' : '' }}>
                        Kurier — 14.99 zł
                    </label>

                    {{-- DODATKOWE POLA DLA PACZKOMATU --}}
                    <div id="inpostFields" class="delivery-extra" style="margin-top:1rem;">

                        <button type="button" onclick="openLockerWidget()" class="btn-green">
                            Wybierz paczkomat InPost
                        </button>

                        <input
                            type="text"
                            id="delivery_point"
                            name="delivery_point"
                            placeholder="Brak wybranego paczkomatu"
                            readonly
                            value="{{ old('delivery_point', session('inpost_point')) }}"
                            style="margin-top:0.5rem;"
                        >

                        <div id="lockerModal" style="display:none;
                            position:fixed; top:0; left:0;
                            width:100%; height:100%;
                            background:rgba(0,0,0,0.6);
                            z-index:9999;
                            justify-content:center; align-items:center;">

                            <div style="width:90%; max-width:600px; height:80%; background:#fff; border-radius:12px; padding:0; position:relative;">

                                <button type="button"
                                        onclick="closeLockerWidget()"
                                        style="position:absolute; top:10px; right:10px;
                                            background:none; border:none; font-size:24px; cursor:pointer;">
                                    ×
                                </button>

                                <!-- TU ŁADUJE SIĘ WIDGET INPOST -->
                                <inpost-geowidget id="lockerWidget"
                                                  token="{{ env('INPOST_GEO_TOKEN') }}"
                                                  language="pl"
                                                  config="parcelCollect"
                                                  onpoint="afterPointSelected">
                                </inpost-geowidget>

                            </div>
                        </div>

                    </div>

                    {{-- DODATKOWE POLA DLA KURIERA --}}
                    <div id="courierFields" class="delivery-extra" style="margin-top:1rem;">
                        <label>Adres dostawy (kurier)</label></br>

                        <input
                            type="text"
                            id="address"
                            name="address"
                            class="checkout-input"
                            placeholder="Ulica i numer domu"
                            value="{{ old('address', session('checkout_address')) }}"
                            style="width:237px;margin-bottom:0.5rem;"
                        ></br>

                        <input
                            type="text"
                            id="postal_code"
                            name="postal_code"
                            class="checkout-input"
                            placeholder="Kod pocztowy"
                            value="{{ old('postal_code', session('checkout_postal_code')) }}"
                            style="width:237px;margin-bottom:0.5rem;"
                        ></br>

                        <input
                            type="text"
                            id="city"
                            name="city"
                            class="checkout-input"
                            placeholder="Miasto"
                            value="{{ old('city', session('checkout_city')) }}"
                            style="width:237px;"
                        >

                    </div>
                </div>

                {{-- PRAWA KOLUMNA – PŁATNOŚĆ --}}
                <div class="checkout-box">
                    <h3>Płatność</h3>

                    <label class="radio-line">
                        <input type="radio" name="payment_method" value="p24"
                            {{ old('payment_method', $paymentMethod) === 'p24' ? 'checked' : '' }}>
                        Przelewy24 (BLIK, przelew, karta)
                    </label>

                    <label class="radio-line">
                        <input type="radio" name="payment_method" value="transfer"
                            {{ old('payment_method', $paymentMethod) === 'transfer' ? 'checked' : '' }}>
                        Przelew tradycyjny
                    </label>
                </div>

            </div>

{{-- KOD RABATOWY --}}
<div class="checkout-box" style="margin-top: 2rem;">
    <h3>Kod rabatowy</h3>

    <form id="couponForm"
          action="{{ route('checkout.coupon') }}"
          method="POST"
          style="display:flex; gap:10px;">
        @csrf

        <input
            type="text"
            name="coupon"
            placeholder="np. RABAT10"
            value="{{ session('coupon_code') }}"
            style="padding:10px; flex:1;"
        >

        <button type="submit" class="btn-apply">
            Zastosuj
        </button>
    </form>

    @if (session('success'))
        <p class="coupon-success">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p class="coupon-error">{{ session('error') }}</p>
    @endif
</div>






            {{-- PODSUMOWANIE --}}
            <div id="summaryBox" class="checkout-box" style="margin-top: 2rem;">
                <h3>Podsumowanie zamówienia</h3>

                <table class="summary-table">
                    <tr>
                        <td>Suma produktów:</td>
                        <td id="sumProducts">{{ number_format($total, 2) }} zł</td>
                    </tr>

                    <tr>
                        <td>Rabat (@if(session('coupon')){{ session('coupon') * 100 }}%@else 0%@endif):</td>
                        <td id="sumDiscount">0 zł</td>
                    </tr>

                    <tr>
                        <td>Dostawa:</td>
                        <td id="sumShipping">0 zł</td>
                    </tr>

                    <tr class="summary-total-row">
                        <td><strong>Razem do zapłaty:</strong></td>
                        <td id="sumTotal"><strong>0 zł</strong></td>
                    </tr>
                </table>
            </div>

            {{-- PRZYCISK DALEJ – FORMULARZ DO PODSUMOWANIA --}}
            <form id="checkout-summary-form"
                action="{{ route('checkout.summary') }}"
                method="POST"
                style="text-align:right; margin-top:2rem;">
                @csrf

                {{-- ukryte pola, uzupełniane w JS przy submit --}}
                <input type="hidden" name="name" id="hidden_name">
                <input type="hidden" name="email" id="hidden_email">
                <input type="hidden" name="phone" id="hidden_phone">
                <input type="hidden" name="delivery_method" id="hidden_delivery_method">
                <input type="hidden" name="payment_method" id="hidden_payment_method">
                <input type="hidden" name="delivery_point" id="hidden_delivery_point">
                <input type="hidden" name="address" id="hidden_address">
                <input type="hidden" name="postal_code" id="hidden_postal_code">
                <input type="hidden" name="city" id="hidden_city">

                <button type="submit" class="checkout-next-btn">
                    Przejdź do podsumowania zamówienia →
                </button>
            </form>



    @endif

</div>

{{-- JS --}}
<!-- <script>
    function openLockerWidget() {
        document.getElementById('lockerModal').style.display = "flex";
    }

    function closeLockerWidget() {
        document.getElementById('lockerModal').style.display = "none";
    }

    // Callback gdy użytkownik wybierze paczkomat
    function afterPointSelected(point) {
        const formatted =
            point.name + " — " +
            point.address.line1 + ", " +
            point.address.city;

        const input = document.getElementById("delivery_point");
        if (input) {
            input.value = formatted;
        }

        closeLockerWidget();
    }

    function toggleDeliveryExtras() {
        const method = document.querySelector('input[name="delivery_method"]:checked')?.value;

        const inpostBox  = document.getElementById('inpostFields');
        const courierBox = document.getElementById('courierFields');

        if (!method) return;

        if (method === 'inpost') {
            inpostBox.style.display  = 'block';
            courierBox.style.display = 'none';
        } else if (method === 'kurier') {
            inpostBox.style.display  = 'none';
            courierBox.style.display = 'block';
        }
    }

    function calculateSummary() {
        let cartTotal = {{ $total ?? 0 }};
        let coupon = {{ session('coupon', 0) }};

        const deliveryRadio = document.querySelector('input[name="delivery_method"]:checked');
        let deliveryMethod = deliveryRadio ? deliveryRadio.value : 'inpost';

        let shipping = 0;

        if (deliveryMethod === 'inpost') shipping = 11.99;
        if (deliveryMethod === 'kurier') shipping = 14.99;

        let discount = cartTotal * coupon;
        let final = cartTotal - discount + shipping;

        document.getElementById('sumProducts').innerText = cartTotal.toFixed(2) + " zł";
        document.getElementById('sumDiscount').innerText = "- " + discount.toFixed(2) + " zł";
        document.getElementById('sumShipping').innerText = shipping.toFixed(2) + " zł";
        document.getElementById('sumTotal').innerHTML = "<strong>" + final.toFixed(2) + " zł</strong>";
    }

    // LISTENERY — dostawa
    document.querySelectorAll('input[name="delivery_method"]').forEach(el => {
        el.addEventListener('change', () => {
            toggleDeliveryExtras();
            calculateSummary();
        });
    });

    // INIT
    toggleDeliveryExtras();
    calculateSummary();
</script> -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const couponForm = document.getElementById('couponForm');

    if (couponForm) {
        couponForm.addEventListener(
            'submit',
            function (e) {
                // Najważniejsze 2 linijki:
                e.stopImmediatePropagation();
                e.stopPropagation();
                // Dzięki temu JS od checkoutu nie "porywa" submita
            },
            true // <-- ważne! przechwytujemy fazę capture
        );
    }
});
    function openLockerWidget() {
        const modal = document.getElementById('lockerModal');
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeLockerWidget() {
        const modal = document.getElementById('lockerModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Callback z geowidgetu InPost – to zostaje jak było, tylko bez fetchów
    // function afterPointSelected(point) {
    //     const formatted =
    //         point.name + ' — ' +
    //         point.address.line1;

    //     const input = document.getElementById('delivery_point');
    //     if (input) {
    //         input.value = formatted;
    //     }


    //     closeLockerWidget();
    // }
    function afterPointSelected(point) {
    const formatted =
        point.name + " — " +
        point.address.line1;

    // pokaż w polu
    document.getElementById('delivery_point').value = formatted;

    // zapisz do ukrytego pola, które pójdzie do SUMMARY POSTEM
    const hidden = document.getElementById('hidden_delivery_point');
    if (hidden) hidden.value = formatted;

    closeLockerWidget();
}


    function toggleDeliveryExtras() {
        const methodInput = document.querySelector('input[name="delivery_method"]:checked');
        const method = methodInput ? methodInput.value : 'inpost';

        const inpostBox  = document.getElementById('inpostFields');
        const courierBox = document.getElementById('courierFields');

        if (inpostBox && courierBox) {
            if (method === 'inpost') {
                inpostBox.style.display  = 'block';
                courierBox.style.display = 'none';
            } else {
                inpostBox.style.display  = 'none';
                courierBox.style.display = 'block';
            }
        }
    }

    function calculateSummary() {
        let cartTotal = {{ $total ?? 0 }};
        let coupon = {{ session('coupon', 0) }};

        const deliveryRadio = document.querySelector('input[name="delivery_method"]:checked');
        let deliveryMethod = deliveryRadio ? deliveryRadio.value : 'inpost';

        let shipping = 0;
        if (deliveryMethod === 'inpost') shipping = 11.99;
        if (deliveryMethod === 'kurier') shipping = 14.99;

        let discount = cartTotal * coupon;
        let final = cartTotal - discount + shipping;

        const sumProducts = document.getElementById('sumProducts');
        const sumDiscount = document.getElementById('sumDiscount');
        const sumShipping = document.getElementById('sumShipping');
        const sumTotal    = document.getElementById('sumTotal');

        if (sumProducts) sumProducts.innerText = cartTotal.toFixed(2) + ' zł';
        if (sumDiscount) sumDiscount.innerText = '- ' + discount.toFixed(2) + ' zł';
        if (sumShipping) sumShipping.innerText = shipping.toFixed(2) + ' zł';
        if (sumTotal)    sumTotal.innerHTML    = '<strong>' + final.toFixed(2) + ' zł</strong>';
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Start
        toggleDeliveryExtras();
        calculateSummary();

        // Zmiana metody dostawy → przelicz jeszcze raz
        document.querySelectorAll('input[name="delivery_method"]').forEach(function (el) {
            el.addEventListener('change', function () {
                toggleDeliveryExtras();
                calculateSummary();
            });
        });

        // (płatność na razie nie zmienia ceny, ale to możesz potem rozbudować)
        document.querySelectorAll('input[name="payment_method"]').forEach(function (el) {
            el.addEventListener('change', function () {
                // ewentualnie logika pod dopłatę za pobranie itp.
            });
        });

        // Obsługa formularza "Przejdź do podsumowania"
        const checkoutForm = document.getElementById('checkout-summary-form');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function () {
                // Dane klienta
                document.getElementById('hidden_name').value  = document.getElementById('name').value;
                document.getElementById('hidden_email').value = document.getElementById('email').value;
                document.getElementById('hidden_phone').value = document.getElementById('phone').value;

                // Metody dostawy i płatności
                const delivery = document.querySelector('input[name="delivery_method"]:checked');
                const payment  = document.querySelector('input[name="payment_method"]:checked');

                document.getElementById('hidden_delivery_method').value = delivery ? delivery.value : 'inpost';
                document.getElementById('hidden_payment_method').value  = payment ? payment.value : 'p24';

                // Paczkomat
                const lockerInput = document.getElementById('delivery_point');
                document.getElementById('hidden_delivery_point').value = lockerInput ? lockerInput.value : '';

                // Adres kuriera
                document.getElementById('hidden_address').value      = document.getElementById('address').value;
                document.getElementById('hidden_postal_code').value  = document.getElementById('postal_code').value;
                document.getElementById('hidden_city').value         = document.getElementById('city').value;

                document.getElementById('checkout-summary-form').addEventListener('submit', function () {
                    document.getElementById('hidden_delivery_point').value =
                    document.getElementById('delivery_point').value;
                });
            });
        }
    });
</script>

@endsection
