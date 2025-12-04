@extends('layout.app')


@section('content')
<div class="checkout-wrapper">

    <h1 class="checkout-title">Dziękujemy za zamówienie!</h1>

    <p>Twój numer zamówienia:</p>

    <h2 style="color:#D4A24C; margin-top:10px;">
        {{ $order->order_number }}
    </h2>

    <p>Na email {{ $order->email }} wyślemy potwierdzenie po zaksięgowaniu płatności.</p>

</div>
@endsection
