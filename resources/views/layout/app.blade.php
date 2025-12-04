<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sklep PAMAT')</title>

    {{-- Font Manrope --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:300,400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="https://geowidget.inpost.pl/inpost-geowidget.css" />
    <script src="https://geowidget.inpost.pl/inpost-geowidget.js" defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="content-wrapper">
<!-- <div class="site-shadow-wrapper"> -->
@php
    $cart = session('cart', []);
    $cartCount = array_sum(array_column($cart, 'quantity'));

    // Pobieramy kategorie potrzebne do nawigacji
    $navCategories = \App\Models\Category::whereIn('name', ['Kawa', 'Herbata', 'Zestawy'])->get();
@endphp

<div class="site-wrapper">
    <header class="site-header">
        <div class="site-header-inner container">

            {{-- LOGO --}}
            <a href="{{ route('home') }}" class="logo logo-img-wrapper">
                <img src="{{ asset('images/logo.png') }}" alt="PAMAT logo" class="logo-img">
            </a>

            {{-- NAWIGACJA --}}
            <nav class="main-nav">

                {{-- KAWA / HERBATA / ZESTAWY --}}
                @foreach($navCategories as $cat)
                    <a href="{{ route('categories.show', $cat->id) }}" class="nav-link">
                        {{ $cat->name }}
                    </a>
                @endforeach

                {{-- STRONY STATYCZNE (na razie placeholdery) --}}
                <a href="{{ route('onas') }}" class="nav-link">O nas</a>


                <a href="{{ route('kontakt') }}" class="nav-link">Kontakt</a>


                {{-- KOSZYK --}}
                <!-- <a href="{{ route('cart.index') }}" class="nav-link nav-cart">
                    Koszyk
                    @if($cartCount > 0)
                        <span class="cart-badge">{{ $cartCount }}</span>
                    @endif
                </a> -->
                <a href="{{ route('cart.index') }}" class="nav-cart">
                    <img src="{{ asset('images/cart-big.svg') }}" alt="Koszyk" class="cart-icon">
                    <span class="cart-badge">{{ $cartCount }}</span>
                </a>


            </nav>
        </div>
    </header>

    <main class="page-content">
        <div class="page-inner">
            @yield('content')
        </div>
    </main>




</div>

<!-- MODAL DODAWANIA DO KOSZYKA -->
<div id="addToCartModal" class="modal-container" style="display: none;" >
    <div class="modal">

        <h2>Wybierz gramaturę</h2>

        <form id="addToCartForm" method="POST">
            @csrf

            <div class="weight-options">
                <label>
                    <input type="radio" name="weight" value="100" checked>
                    100g
                </label>

                <label>
                    <input type="radio" name="weight" value="200">
                    200g
                </label>

                <label>
                    <input type="radio" name="weight" value="custom">
                    Inna waga:
                    <input type="number" id="customWeight" name="custom_weight" placeholder="np. 150" min="1" disabled>
                </label>
            </div>

            <div class="modal-buttons">


                <button type="submit" class="btn-confirm" formaction="" id="addContinueBtn">
                    Dodaj i kontynuuj zakupy
                </button>

                <button type="submit" class="btn-go-cart" formaction="" id="addGoCartBtn">
                    Dodaj i przejdź do koszyka
                </button>

                <button type="button" class="btn-cancel" onclick="closeModal()">Anuluj</button>
            </div>

        </form>
    </div>
</div>

<!-- <script>
let currentProductId = null; -->

<div id="weightModal" style="display: none;" class="pm-modal" >
    <div class="pm-modal-content">
        <h2 class="pm-modal-title">
            <span id="modalProductName"></span>
        </h2>
        <form id="weightForm" method="POST">
    @csrf

    <!-- 📌 TU POWSTAJE DYNAMICZNA TREŚĆ -->
    <div id="modalDynamicFields"></div>

    <div class="pm-btn-row">


        <button type="submit" name="goto" value="back" class="pm-btn pm-btn-main">
            Dodaj i wróć do zakupów
        </button>

        <button type="submit" name="goto" value="cart" class="pm-btn pm-btn-accent">
            Dodaj i przejdź do koszyka
        </button>

        <button type="button" class="pm-btn pm-btn-cancel" onclick="closeWeightModal()">
            Anuluj
        </button>
    </div>
</form>

    </div>
</div>
</div>

<script>

function openWeightModal(productId, productName, isSet = false) {
    console.log("openWeightModal:", {
    productId,
    productName,
    isSet
});

    document.getElementById("weightModal").style.display = "flex";
    document.getElementById("modalProductName").textContent = productName;

    const form = document.getElementById("weightForm");
    form.action = `/cart/add-weight/${productId}`;

    const box = document.getElementById("modalDynamicFields");

    if (isSet) {
        // =============================
        // ZESTAW → wybór ilości sztuk
        // =============================
        box.innerHTML = `
            <label class="pm-label">Wybierz ilość sztuk:</label>
            <input type="number"
                   name="set_quantity"
                   class="pm-input"
                   min="1"
                   step="1"
                   value="1">
        `;
    } else {
        // ====================================
        // HERBATA / KAWA → wybór gramatury
        // ====================================
        box.innerHTML = `
            <label class="pm-label">Wybierz wagę:</label>

            <div class="pm-weight-options">
                <label class="pm-radio">
                    <input type="radio" name="weight" value="100" checked>
                    <span>100 g</span>
                </label>

                <label class="pm-radio">
                    <input type="radio" name="weight" value="200">
                    <span>200 g</span>
                </label>

                <label class="pm-radio">
                    <input type="radio" name="weight" value="custom" id="radioCustom">
                    <span>Własna waga</span>
                </label>
            </div>

            <input
                type="number"
                name="custom_weight"
                id="customWeightInput"
                class="pm-input"
                placeholder="np. 150"
                style="display:none"
            />
        `;
    }
}

function closeWeightModal() {
    document.getElementById("weightModal").style.display = "none";
}

document.addEventListener("change", function (e) {
    if (e.target.id === "radioCustom") {
        document.getElementById("customWeightInput").style.display = "block";
    }

    if (e.target.name === "weight" && e.target.value !== "custom") {
        const customInput = document.getElementById("customWeightInput");
        if (customInput) {
            customInput.style.display = "none";
        }
    }
});

</script>

@if(session('added_product'))
    <div id="pm-toast-backdrop" class="pm-toast-backdrop" onclick="pmHideToast()"></div>

    <div id="pm-toast-modal" class="pm-toast-modal">
        <div class="pm-toast-content">
            <img src="{{ Storage::url(session('added_product')['image']) }}" class="pm-toast-img" alt="">
            <div>
                <h3>Dodano do koszyka!</h3>
                <p>{{ session('added_product')['name'] }}</p>
            </div>
        </div>
    </div>
@endif

<script>
function pmHideToast() {
    const modal = document.getElementById('pm-toast-modal');
    const backdrop = document.getElementById('pm-toast-backdrop');

    if (modal) {
        modal.style.animation = "fadeOutToast .25s forwards";
    }
    if (backdrop) {
        backdrop.style.opacity = "0";
        backdrop.style.transition = "0.25s";
    }

    setTimeout(() => {
        if (modal) modal.style.display = "none";
        if (backdrop) backdrop.style.display = "none";
    }, 250);
}

setTimeout(pmHideToast, 1000);
</script>

@if(!request()->cookie('cookies_accepted'))
<div id="cookies-popup" class="cookies-popup">
    <div class="cookies-box">

        <p>
            Używamy plików cookies, aby zapewnić najlepszą jakość korzystania z naszego sklepu.
            Kontynuując, zgadzasz się na ich użycie.
        </p>

        <form action="{{ route('cookies.accept') }}" method="POST" class="cookies-buttons">
            @csrf
            <button type="submit" class="cookies-btn accept">Akceptuję</button>

            <a href="{{ route('privacy') }}" class="cookies-btn info">
                Dowiedz się więcej
            </a>
        </form>

    </div>
</div>
@endif

<script>
function changeImage(url) {
    document.getElementById('mainImage').src = url;
}
</script>
</body>
    <footer class="site-footer">
        <div class="container footer-inner">

            <div class="footer-left">
                © {{ date('Y') }} PAMAT. Wszystkie prawa zastrzeżone.
            </div>

            <div class="footer-right">
                <a href="{{ route('returns') }}" class="footer-link">Polityka zwrotu</a>
                <a href="{{ route('privacy') }}" class="footer-link">Polityka prywatności</a>
                <a href="{{ route('regulamin') }}" class="footer-link">Regulamin</a>
            </div>

        </div>
    </footer>
</html>
