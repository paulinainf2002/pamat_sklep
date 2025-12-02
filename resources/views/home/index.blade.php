@extends('layout.app')

@section('content')
<div style="    max-width: 1320px;
    background: #fff;
    border-radius: 14px;
    padding: 0.5rem;
    width: 100%;
">
{{-- HERO SECTION --}}


<div class="hero-section-wrapper">
    <div class="container">
        <div class="hero-flex">

            <div class="hero-text">
                <h1>Prawdziwy smak <br> codzienności</h1>

                <p class="hero-subtitle bold">
                    PaMat to prostota i przyjemność w codziennym życiu.
                </p>

                <p class="hero-subtitle small">
                    Twoje rytuały, Twoje chwile.
                </p>
            </div>

            <div class="hero-photo">
                <img src="/images/hero-circle.png" alt="PaMat">
            </div>

        </div>
    </div>
</div>





{{-- NOWOŚCI --}}
<section class="carousel-section">
    <h2>Nowości</h2>

    <div class="carousel">
        <button class="carousel-btn prev" data-carousel="new">&lt;</button>

        <div class="carousel-track" id="carousel-new">
            @foreach($newProducts as $product)
                <div class="carousel-item">
                    <a href="{{ route('products.show', $product->id) }}">
                        <div class="carousel-card">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}">
                            @endif
                            <h3>{{ $product->name }}</h3>
                            <p>{{ number_format($product->price, 2) }} zł</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <button class="carousel-btn next" data-carousel="new">&gt;</button>
    </div>
</section>



{{-- NAJPOPULARNIEJSZE --}}
<section class="carousel-section">
    <h2>Najpopularniejsze</h2>

    <div class="carousel">
        <button class="carousel-btn prev" data-carousel="popular">&lt;</button>

        <div class="carousel-track" id="carousel-popular">
            @foreach($popularProducts as $product)
                <div class="carousel-item">
                    <a href="{{ route('products.show', $product->id) }}">
                        <div class="carousel-card">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}">
                            @endif
                            <h3>{{ $product->name }}</h3>
                            <p>
                                {{ number_format($product->price, 2) }} zł
                            </p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <button class="carousel-btn next" data-carousel="popular">&gt;</button>
    </div>
</section>
</div>

@endsection
