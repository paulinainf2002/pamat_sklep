@extends('layout.app')

@section('title', 'Regulamin – PaMat')

@section('content')

<section class="policy-section container">

    <h1 class="policy-title">Regulamin sklepu PaMat</h1>

    <p class="policy-lead">
        Niniejszy regulamin określa zasady korzystania ze sklepu internetowego PaMat,
        składania zamówień, realizacji płatności oraz praw i obowiązków Konsumenta.
    </p>

    <div class="policy-box">

        <h2>1. Informacje ogólne</h2>
        <p>
            1. Sklep internetowy PaMat działa pod adresem <strong>pamat.pl</strong>
            i prowadzony jest przez PaMat, ul. Dąbrowskiego 16, 32-651 Nowa Wieś.
        </p>
        <p>
            2. Kontakt ze sklepem możliwy jest przez:
        </p>
        <ul>
            <li>Email: <a href="mailto:grpamat@gmail.com">grpamat@gmail.com</a></li>
            <li>Telefon: 518 300 221</li>
        </ul>

        <h2>2. Definicje</h2>
        <p><strong>Konsument</strong> – osoba fizyczna dokonująca zakupu niezwiązanego z działalnością gospodarczą.</p>
        <p><strong>Sprzedawca</strong> – PaMat, dane jak wyżej.</p>
        <p><strong>Produkt</strong> – towary dostępne w sklepie, głównie kawa, herbata i zestawy.</p>

        <h2>3. Składanie zamówień</h2>
        <ul>
            <li>Zamówienia można składać 24/7.</li>
            <li>Złożenie zamówienia oznacza zawarcie umowy sprzedaży.</li>
            <li>Kupujący jest zobowiązany do podania prawdziwych danych.</li>
        </ul>

        <h2>4. Ceny i płatności</h2>
        <ul>
            <li>Wszystkie ceny podane są w złotówkach i zawierają podatek VAT.</li>
            <li>Płatność możliwa jest m.in. przez przelew lub systemy płatności elektronicznych.</li>
            <li>Cena podana przy produkcie w momencie zakupu jest wiążąca.</li>
        </ul>

        <h2>5. Dostawa</h2>
        <ul>
            <li>Produkty wysyłane są na adres podany przez Kupującego.</li>
            <li>Termin dostawy wynosi zwykle 1–3 dni robocze.</li>
            <li>Wysyłka realizowana jest przez firmy kurierskie.</li>
        </ul>

        <h2>6. Zwroty i reklamacje</h2>
        <p>
            Zasady zwrotów opisane są w zakładce
            <a href="{{ route('returns') }}">Polityka zwrotu</a>.
        </p>
        <p>
            Reklamacje rozpatrywane są do 7 dni roboczych od momentu zgłoszenia.
        </p>

        <h2>7. Dane osobowe</h2>
        <p>
            Dane osobowe przetwarzane są zgodnie z
            <a href="{{ route('privacy') }}">Polityką prywatności</a>.
        </p>

        <h2>8. Postanowienia końcowe</h2>
        <ul>
            <li>Regulamin może ulec zmianie — aktualna wersja publikowana jest na stronie.</li>
            <li>W sprawach nieuregulowanych obowiązuje prawo polskie.</li>
            <li>Akceptacja regulaminu jest wymagana przy składaniu zamówienia.</li>
        </ul>

    </div>

</section>

@endsection
