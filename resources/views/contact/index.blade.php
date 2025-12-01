@extends('layout.app')

@section('title', 'Kontakt – PaMat')

@section('content')

<section class="contact-section container">

    <div class="contact-wrapper">

        {{-- LEWA STRONA – DANE KONTAKTOWE --}}
        <div class="contact-info">

            <h1>Skontaktuj się z nami</h1>

            <p class="contact-lead">
                Masz pytania dotyczące naszych produktów lub zamówień?
                Jesteśmy tutaj, aby pomóc.
            </p>

            <div class="contact-details">
                <p><strong>Email:</strong> <a href="mailto:grpamat@gmail.com">grpamat@gmail.com</a></p>
                <p><strong>Telefon:</strong> <a href="tel:518300221">518 300 221</a></p>
                <p><strong>Adres:</strong> ul. Dąbrowskiego 16, 32-651 Nowa Wieś</p>
            </div>

            <div class="contact-details2">
                <p><strong>NIP:</strong> 5492460915</p>
                <p><strong>REGON:</strong> 387591024</p>
            </div>

            <div class="contact-socials">
                <p>Znajdziesz nas:</p>
                <div class="social-links">
                    <a href="https://www.instagram.com/sklep_pamat/">Instagram</a>
                    <!-- <a href="#">Facebook</a> -->
                </div>
            </div>

        </div>

        {{-- PRAWA STRONA – FORMULARZ --}}
        <div class="contact-form">
            <h2>Napisz do nas</h2>

            <form>
                @csrf
                <input type="text" placeholder="Twoje imię" required>
                <input type="email" placeholder="Twój e-mail" required>
                <textarea placeholder="Wiadomość..." required></textarea>

                <button type="submit">Wyślij wiadomość</button>
            </form>
        </div>

    </div>

</section>

@endsection
