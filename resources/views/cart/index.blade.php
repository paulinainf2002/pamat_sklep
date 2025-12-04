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
                        class="checkout-input"
                        placeholder="Jan Kowalski"
                        value="{{ session('checkout_name') }}"
                    >
                </div>

                <div class="checkout-field">
                    <label>Adres e-mail</label></br>
                    <input
                        type="email"
                        id="email"
                        class="checkout-input"
                        placeholder="jan.kowalski@example.com"
                        value="{{ session('checkout_email') }}"
                    >
                </div>

                <div class="checkout-field">
                    <label>Telefon</label></br>
                    <input
                        type="text"
                        id="phone"
                        class="checkout-input"
                        placeholder="123 456 789"
                        value="{{ session('checkout_phone') }}"
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
                        {{ $deliveryMethod === 'inpost' ? 'checked' : '' }}>
                    Paczkomat InPost — 11.99 zł
                </label>

                <label class="radio-line">
                    <input type="radio" name="delivery_method" value="kurier"
                        {{ $deliveryMethod === 'kurier' ? 'checked' : '' }}>
                    Kurier — 14.99 zł
                </label>

                {{-- DODATKOWE POLA DLA PACZKOMATU --}}
                <div id="inpostFields" class="delivery-extra" style="margin-top:1rem;">
                    <label>Wybrany paczkomat</label>
                    <div class="coupon-row">
                       <button type="button" onclick="openInpostWidget()" class="btn-green">
                            Wybierz paczkomat
                        </button>

                        <input type="text" id="delivery_point" readonly placeholder="Brak wybranego paczkomatu">

                    </div>
                </div>

                {{-- DODATKOWE POLA DLA KURIERA --}}
                <div id="courierFields" class="delivery-extra" style="margin-top:1rem;">
                    <label>Adres dostawy (kurier)</label></br>

                        <input
                            type="text"
                            id="address"
                            class="checkout-input"
                            placeholder="Ulica i numer domu"
                            value="{{ session('checkout_address') }}"
                            style="width:237px;margin-bottom:0.5rem;"
                        ></br>


                        <input
                            type="text"
                            id="postal_code"
                            class="checkout-input"
                            placeholder="Kod pocztowy"
                            value="{{ session('checkout_postal_code') }}"
                            style="width:237px;margin-bottom:0.5rem;"
                        ></br>

                        <input
                            type="text"
                            id="city"
                            class="checkout-input"
                            placeholder="Miasto"
                            value="{{ session('checkout_city') }}"
                            style="width:237px;"
                        >

                </div>
            </div>

            {{-- PRAWA KOLUMNA – PŁATNOŚĆ --}}
            <div class="checkout-box">
                <h3>Płatność</h3>

                <label class="radio-line">
                    <input type="radio" name="payment_method" value="p24"
                        {{ $paymentMethod === 'p24' ? 'checked' : '' }}>
                    Przelewy24 (BLIK, przelew, karta)
                </label>

                <label class="radio-line">
                    <input type="radio" name="payment_method" value="transfer"
                        {{ $paymentMethod === 'transfer' ? 'checked' : '' }}>
                    Przelew tradycyjny
                </label>
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

        {{-- PODSUMOWANIE --}}
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

    @endif
    <!-- MODAL MAPY INPOST -->
<div id="geowidget-modal"></div>




</div>

{{-- JS --}}
<script>
    const updateFieldUrl = "{{ route('checkout.updateField') }}";
    const csrfToken = "{{ csrf_token() }}";

    function updateField(field, value) {
        fetch(updateFieldUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ field, value }),
        }).catch(() => {});
    }
function openInpostWidget() {

    InPost.Geowidget.bootstrap({
        token: "{{ env('INPOST_GEO_TOKEN') }}",
        config: {
            target: "#geowidget-modal", // element gdzie widget ma się pojawić
            modalMode: true,            // automatyczny modal
            language: "pl"
        },
        onPick: function(point) {

            const full =
                point.name + " — " +
                point.address.line1 + ", " +
                point.address.city;

            document.getElementById("delivery_point").value = full;

            fetch("{{ route('checkout.shipping.point') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    locker: full,
                    locker_id: point.name,
                    locker_full: point
                })
            });

            console.log("Wybrano paczkomat:", point);
        }
    });
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

    // LISTENERY — dane klienta
    ['name', 'email', 'phone'].forEach((id) => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', (e) => {
                updateField(id, e.target.value);
            });
        }
    });

    // LISTENERY — adres kuriera
    ['address', 'city', 'postal_code'].forEach((id) => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', (e) => {
                updateField(id, e.target.value);
            });
        }
    });

    // LISTENERY — dostawa
    document.querySelectorAll('input[name="delivery_method"]').forEach(el => {
        el.addEventListener('change', () => {
            updateField('delivery_method', el.value);
            toggleDeliveryExtras();
            calculateSummary();
        });
    });

    // LISTENERY — płatność
    document.querySelectorAll('input[name="payment_method"]').forEach(el => {
        el.addEventListener('change', () => {
            updateField('payment_method', el.value);
        });
    });

    // INIT
    toggleDeliveryExtras();
    calculateSummary();
</script>


@endsection
