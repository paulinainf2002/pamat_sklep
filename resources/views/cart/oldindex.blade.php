@extends('layout.app')

@section('content')

<h2>Koszyk</h2>

@if (session('success'))
    <div style="background:#ddffdd; padding:10px; border-radius:8px; margin-bottom:20px;">
        {{ session('success') }}
    </div>
@endif

@if (empty($cart))
    <p>Twój koszyk jest pusty.</p>
@else

    <table style="width:100%; background:white; padding:20px; border-radius:10px;">
        <tr>
            <th style="text-align:left; padding:10px;">Produkt</th>
            <th style="text-align:left; padding:10px;">Cena</th>
            <th style="text-align:left; padding:10px;">Ilość</th>
            <th style="text-align:left; padding:10px;">Usuń</th>
        </tr>

        @foreach ($cart as $id => $item)
            <tr>
                <td style="padding:10px;">
                    <strong>{{ $item['name'] }}</strong><br>
                    @if ($item['image'])
                        <img src="{{ asset('storage/' . $item['image']) }}" width="80">
                    @endif
                </td>

                <td style="padding:10px;">
                    {{ number_format($item['price'], 2) }} zł
                </td>

                <td style="padding:10px;">
                    {{ $item['quantity'] }}
                </td>

                <td style="padding:10px;">
                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                        @csrf
                        <button style="background:red; color:white; padding:5px 10px; border:none; border-radius:5px;">
                            Usuń
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach

    </table>

    <form action="{{ route('cart.clear') }}" method="POST" style="margin-top:20px;">
        @csrf
        <button style="padding:10px 20px; background:#444; color:white; border:none; border-radius:5px;">
            Wyczyść koszyk
        </button>
    </form>

@endif

@endsection
