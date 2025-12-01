@extends('layout.app')

@section('title', 'O nas – PaMat')

@section('content')
<!-- <link rel="stylesheet" href="{{ asset('css/onas.css') }}"> -->

<section class="onas-section container">

    <div class="onas-wrapper">

        {{-- LEWA STRONA – TEKST --}}
        <div class="onas-text">
            <h1>O nas</h1>

            <p>
                W PaMat wierzymy, że codzienność zasługuje na małe rytuały — chwile spokoju, aromatu i ciepła,
                które przywracają równowagę.
            </p>

            <p>
                Jesteśmy rodzinną marką tworzoną z pasji do natury, designu i wyjątkowych smaków.
                Wybieramy tylko najlepsze składniki, współpracując z zaufanymi dostawcami
                i dbając o każdy etap produkcji — od ziaren i liści, po opakowanie.
            </p>

            <p>
                Naszą misją jest dostarczanie produktów, które nie tylko smakują,
                ale też tworzą atmosferę. Chcemy, aby każda filiżanka była dla Ciebie
                momentem wytchnienia i przyjemności.
            </p>

            <h2>
                PaMat — smak, który łączy pokolenia.
            </h2>
        </div>

        {{-- PRAWA STRONA – ZDJĘCIE --}}
        <div class="onas-photo">
            <img src="{{ asset('images/onas-photo.png') }}" alt="PaMat – nasz zespół i pasja">
        </div>

    </div>

</section>
@endsection
