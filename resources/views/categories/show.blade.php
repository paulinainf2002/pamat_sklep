@extends('layout.app')

@section('content')

@php
    $selected = (array) request('categories', [$category->id]);
@endphp
<!-- <div class="page-inner"> -->
<div class="category-page">

    {{-- LEWY PANEL - FILTRY --}}
    <aside class="filters">

        <h3>Filtry</h3>
        <!-- <div class="filter-box"> -->
            <h4>Szukaj kategorii</h4>
            <input type="text" id="categorySearch" placeholder="Wpisz np. zielona">
        <!-- </div> -->

        {{-- PRODUKT / RODZAJ / SMAK – rozwijane sekcje --}}
        @php
            $groupsOrder = ['produkt' => 'Produkt', 'rodzaj' => 'Rodzaj', 'smak' => 'Smak'];
        @endphp

        @foreach($groupsOrder as $groupKey => $groupLabel)
            @if(isset($categoriesByGroup[$groupKey]))
                @php
                    // sprawdzamy czy w tej grupie jest jakakolwiek zaznaczona kategoria
                    $openSection = false;

                    foreach($categoriesByGroup[$groupKey] as $cat) {
                        if (in_array($cat->id, $selected)) {
                            $openSection = true;
                            break;
                        }
                    }
                @endphp

                <details class="filter-group" {{ $openSection ? 'open' : '' }}>

                    <summary>{{ $groupLabel }}</summary>
                    <ul>
                        @foreach($categoriesByGroup[$groupKey] as $cat)
                            <li class="checkbox-item">
                                <label>
                                    <input type="checkbox"
                                           name="categories[]"
                                           form="filters-form"
                                           value="{{ $cat->id }}"
                                           {{ in_array($cat->id, $selected) ? 'checked' : '' }}>
                                    {{ $cat->name }} ({{ $counts[$cat->id] ?? 0 }})
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif
        @endforeach

        {{-- Wyszukiwarka --}}
        <div class="filter-box">
            <h4>Wyszukaj</h4>
            <input type="text" name="search" form="filters-form" placeholder="Nazwa produktu"
                   value="{{ request('search') }}">
        </div>

        {{-- Zakres cen --}}
        <div class="filter-box">
            <h4>Cena</h4>
            <div class="price-range">
                <input type="number" name="min_price" form="filters-form" placeholder="Min"
                       value="{{ request('min_price') }}">
                <input type="number" name="max_price" form="filters-form" placeholder="Max"
                       value="{{ request('max_price') }}">
            </div>
        </div>

        {{-- Sortowanie --}}
        <div class="filter-box">
            <h4>Sortowanie</h4>
            <select name="sort" form="filters-form">
                <option value="">Domyślne</option>
                <option value="price_asc"  {{ request('sort')=='price_asc' ? 'selected' : '' }}>Cena rosnąco</option>
                <option value="price_desc" {{ request('sort')=='price_desc' ? 'selected' : '' }}>Cena malejąco</option>
                <option value="name_asc"   {{ request('sort')=='name_asc' ? 'selected' : '' }}>Nazwa A-Z</option>
                <option value="name_desc"  {{ request('sort')=='name_desc' ? 'selected' : '' }}>Nazwa Z-A</option>
            </select>
        </div>
        <button type="button"
                class="btn-clear"
                onclick="window.location='{{ route('categories.show', $category->id) }}'">
            Usuń wszystkie filtry
        </button>

        {{-- Zastosuj --}}
        <button type="submit" form="filters-form" class="btn-primary" style="margin-top: 1rem;">
            Zastosuj filtr
        </button>

        {{-- Ukryty formularz GET --}}
        <form id="filters-form" method="GET"></form>

    </aside>




    {{-- PRAWA STRONA - PRODUKTY --}}
    <section class="products-list">

        <h2>{{ $category->name }}</h2>

        <div class="product-grid">
@forelse($products as $product)
@php
    $isSet = $product->categories->contains('id', 6);
@endphp

    <div class="product-card">

        <div class="product-image">
            @if($product->image)
                <!-- <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"> -->
                     <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" />

            @endif
        </div>

        <h3 class="product-name">{{ $product->name }}</h3>

        <p class="product-price">
            {{ number_format($product->price, 2) }} zł
            <span class="unit">
                @if($isSet)
                    / zestaw
                @else
                    / 100g
                @endif
            </span>
        </p>

        <div class="product-actions">
            <a href="{{ route('products.show', $product->id) }}" class="btn-more">Zobacz więcej</a>

            <!-- <button type="button"
                onclick="openWeightModal({{ $product->id }}, '{{ $product->name }}', {{ $isSet ? 'true' : 'false' }})"
                class="btn-add">
                Dodaj do koszyka
            </button> -->
            <button
    type="button"
    class="btn-add"
    onclick="openWeightModal(
        {{ $product->id }},
        '{{ $product->name }}',
        {{ $product->categories->contains('id', 6) ? 'true' : 'false' }}
    )">
    Dodaj do koszyka
</button>

        </div>

    </div>
<!-- </div> -->
@empty
    <p>Brak produktów spełniających filtrowanie.</p>
@endforelse
</div>

        <div style="margin-top: 2rem;">
            {{ $products->links() }}
        </div>

    </section>

</div>

<script>
document.getElementById('categorySearch').addEventListener('input', function () {
    const term = this.value.toLowerCase();

    const groups = document.querySelectorAll('.filter-group');
    const items  = document.querySelectorAll('.checkbox-item');

    groups.forEach(group => {
        let foundInGroup = false;

        const checkboxes = group.querySelectorAll('.checkbox-item');

        checkboxes.forEach(item => {
            const text = item.innerText.toLowerCase();

            if (text.includes(term)) {
                item.style.display = 'flex';
                foundInGroup = true;
            } else {
                item.style.display = term === '' ? 'flex' : 'none';
            }
        });

        // Jeśli znaleziono coś w danej grupie → otwieramy ją
        group.open = foundInGroup || term === '';
    });
});
</script>


@endsection
