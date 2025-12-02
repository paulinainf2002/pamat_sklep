

@extends('layout.app')

@section('content')

<div class="product-page container">

    {{-- PRZYCISK WSTECZ --}}
    <a href="{{ url()->previous() }}" class="back-btn">← Wróć</a>

    <div class="product-wrapper">

        {{-- LEWA KOLUMNA --}}
        <div class="left-col">

            <!-- <div class="product-image-big">
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
            </div> -->
            <!-- <div class="product-gallery">

                {{-- Główne zdjęcie --}}
                <div class="product-image-big">
                    <img id="mainImage" src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                </div>

                {{-- Miniaturki --}}
                <div class="product-thumbs">
                    <img onclick="changeImage('{{ Storage::url($product->image) }}')"
                        src="{{ Storage::url($product->image) }}"
                        class="thumb">

                    @if($product->image2)
                        <img onclick="changeImage('{{ asset('storage/' . $product->image2) }}')"
                            src="{{ asset('storage/' . $product->image2) }}"
                            class="thumb">
                @endif
                </div>

            </div> -->
            <div class="product-gallery">

    <div class="gallery-wrapper">

        <button class="gallery-arrow left" onclick="prevImage()">❮</button>

        <img id="galleryMain"
             src="{{ Storage::url($product->image) }}"
             class="gallery-main">

        <button class="gallery-arrow right" onclick="nextImage()">❯</button>
    </div>

    {{-- MINIATURKI --}}
    <div class="gallery-thumbs">

        <img src="{{ Storage::url($product->image) }}"
             class="thumb active-thumb"
             onclick="setImage(0)">

        @if($product->image2)
            <img src="{{ asset('storage/' . $product->image2) }}"
                 class="thumb"
                 onclick="setImage(1)">
        @endif

    </div>

</div>

<script>
    // lista zdjęć
    const galleryImages = [
        "{{ Storage::url($product->image) }}",
        @if($product->image2)
            "{{ asset('storage/' . $product->image2) }}",
        @endif
    ];

    let currentIndex = 0;

    function setImage(index) {
        currentIndex = index;
        document.getElementById('galleryMain').src = galleryImages[index];

        document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active-thumb'));
        document.querySelectorAll('.thumb')[index].classList.add('active-thumb');
    }

    function nextImage() {
        currentIndex = (currentIndex + 1) % galleryImages.length;
        setImage(currentIndex);
    }

    function prevImage() {
        currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
        setImage(currentIndex);
    }
</script>



            <div class="product-info-list">
                <p><strong>Kraj pochodzenia:</strong> {{ $product->origin ?? 'Brak danych' }}</p>
                <p><strong>Skład:</strong> {{ $product->ingredients ?? 'Brak danych' }}</p>
                <!-- <p><strong>Charakterystyka:</strong> {{ $product->description ?? 'Brak danych' }}</p> -->
                <p><strong>Kategorie:</strong>
                    @foreach($product->categories as $cat)
                        <span class="tag">{{ $cat->name }}</span>
                    @endforeach
                </p>
            </div>

        </div>

        {{-- PRAWA KOLUMNA --}}
            <div class="right-col">

                {{-- NAZWA --}}
                <h1 class="product-title-big">{{ $product->name }}</h1>

                {{-- OPIS --}}
                <p class="product-desc-big">
                    {{ $product->description ?? 'Brak opisu produktu.' }}
                </p>

                {{-- BLOK CENA + DOSTAWA + PRZYCISK --}}
                <div class="product-purchase-box">

                    @php
                        $isSet = $product->categories->contains('id', 6);
                    @endphp

                    <p class="product-price-big">
                        {{ number_format($product->price, 2) }} zł
                        <span class="unit-big">{{ $isSet ? '/ zestaw' : '/ 100g' }}</span>
                    </p>

                    <p class="delivery-note-big">Dostawa od 14,99 zł</p>

                    <button class="add-btn-big"
                        onclick="openWeightModal({{ $product->id }}, '{{ $product->name }}', {{ $isSet ? 'true' : 'false' }})">
                        Dodaj do koszyka
                    </button>

                </div>

            </div>



    </div>

</div>

@endsection
