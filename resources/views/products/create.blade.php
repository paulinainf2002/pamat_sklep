@extends('layout.app')

@section('content')

<h2 style="color:#3E4E20; margin-bottom:1.5rem;">Dodaj nowy produkt</h2>

@if ($errors->any())
    <div style="background:#ffdddd; padding:12px; border-radius:8px; margin-bottom:20px;">
        <strong>Błędy:</strong>
        <ul style="margin-top:10px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
      style="background:white; padding:2rem; border-radius:12px; max-width:800px; box-shadow:0 6px 20px rgba(0,0,0,0.07);">
    @csrf

    {{-- NAZWA --}}
    <label>Nazwa produktu:</label>
    <input type="text" name="name" required
           style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:8px;">

    {{-- CENA --}}
    <label>Cena (za 100g lub zestaw):</label>
    <input type="number" step="0.01" name="price" required
           style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:8px;">

    {{-- KATEGORIE --}}
    <label>Kategorie:</label>
    <div style="margin-bottom:15px;">
        @foreach($categories as $category)
            <label style="display:inline-block; margin-right:15px;">
                <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                {{ $category->name }}
            </label>
        @endforeach
    </div>

    {{-- KRAJ POCHODZENIA --}}
    <label>Kraj pochodzenia:</label>
    <input type="text" name="origin"
           placeholder="np. Chiny, Brazylia, Indie"
           style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:8px;">

    {{-- SKŁAD --}}
    <label>Skład:</label>
    <textarea name="ingredients" rows="3"
              placeholder="np. zielona herbata, aromat waniliowy..."
              style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:8px;"></textarea>

    {{-- OPIS / CHARAKTERYSTYKA --}}
    <label>Opis / charakterystyka:</label>
    <textarea name="description" rows="5"
              placeholder="Opisz smak, aromat, działanie, cechy produktu..."
              style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:8px;"></textarea>

    {{-- ZDJĘCIE --}}
    <label>Zdjęcie produktu:</label>
    <input type="file" name="image" accept="image/*"
           style="margin-bottom:20px;">

     {{-- ZDJĘCIE 2 — NOWE POLE --}}
    <label>Dodatkowe zdjęcie produktu:</label>
    <input type="file" name="image2" accept="image/*"
           style="margin-bottom:20px; display:block;">
    <button type="submit"
            style="padding:12px 24px; background:#3E4E20; color:white; border:none; cursor:pointer; border-radius:8px; font-size:1rem;">
        Dodaj produkt
    </button>

</form>

@endsection
