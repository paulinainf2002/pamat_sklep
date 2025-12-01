@extends('layout.app')

@section('title', 'Polityka zwrotu – PaMat')

@section('content')

<section class="policy-section container">

    <h1 class="policy-title">Polityka zwrotu</h1>

    <p class="policy-lead">
        Zależy nam, aby każdy klient PaMat był w pełni zadowolony z zakupu.
        Jeśli produkt nie spełnia Twoich oczekiwań, możesz go zwrócić zgodnie z poniższymi zasadami.
    </p>

    <div class="policy-box">

        <h2>1. Czas na odstąpienie od umowy</h2>
        <p>
            Masz prawo odstąpić od umowy w terminie <strong>14 dni</strong> od otrzymania zamówienia,
            bez podawania przyczyny.
        </p>

        <h2>2. Produkty, które podlegają zwrotowi</h2>
        <p>
            Zwrotowi podlegają wszystkie produkty w stanie nienaruszonym — czyli
            <strong>nieotwierane</strong>, <strong>nieuszkodzone</strong> i w oryginalnym opakowaniu.
            Produkty spożywcze po otwarciu lub naruszeniu zabezpieczenia nie mogą zostać zwrócone.
        </p>

        <h2>3. Jak zgłosić zwrot?</h2>
        <p>Aby zgłosić zwrot, skontaktuj się z nami:</p>

        <ul>
            <li>Email: <a href="mailto:grpamat@gmail.com">grpamat@gmail.com</a></li>
            <li>Telefon: 518 300 221</li>
        </ul>

        <p>
            W wiadomości podaj:
        </p>

        <ul>
            <li>numer zamówienia,</li>
            <li>produkty, które chcesz zwrócić,</li>
            <li>powód zwrotu (opcjonalnie).</li>
        </ul>

        <h2>4. Adres do zwrotów</h2>
        <p>
            PaMat
            <br>ul. Dąbrowskiego 16
            <br>32-651 Nowa Wieś
        </p>

        <h2>5. Koszt przesyłki zwrotnej</h2>
        <p>
            Koszt odesłania produktu pokrywa klient, chyba że zwrot wynika z błędu
            z naszej strony (np. uszkodzony produkt, pomyłka w zamówieniu).
        </p>

        <h2>6. Zwrot pieniędzy</h2>
        <p>
            Po otrzymaniu i sprawdzeniu zwracanego produktu dokonamy zwrotu kosztów
            w ciągu <strong>7 dni roboczych</strong> tą samą metodą, którą dokonano płatności.
        </p>

        <h2>7. Reklamacje</h2>
        <p>
            Jeśli przesyłka dotarła uszkodzona lub produkt jest wadliwy — natychmiast skontaktuj się z nami.
            Reklamacje rozpatrujemy maksymalnie w ciągu <strong>7 dni roboczych</strong>.
        </p>
    </div>

</section>

@endsection
