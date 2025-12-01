<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($id)
    {
        $category = Category::findOrFail($id);

        // Wybrane kategorie (domyślnie aktualna)
        $selectedCategories = request('categories', [$id]);

        // TRYB AND – każdy whereHas osobno
        $query = Product::query();

        foreach ($selectedCategories as $catId) {
            $query->whereHas('categories', function ($q) use ($catId) {
                $q->where('categories.id', $catId);
            });
        }

        // Wyszukiwarka
        if (request()->filled('search')) {
            $query->where('name', 'LIKE', '%' . request('search') . '%');
        }

        // Zakres cen
        if (request()->filled('min_price')) {
            $query->where('price', '>=', request('min_price'));
        }
        if (request()->filled('max_price')) {
            $query->where('price', '<=', request('max_price'));
        }

        // Sortowanie
        if (request()->filled('sort')) {
            switch (request('sort')) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12);

        // Kategorie pogrupowane
        $categoriesByGroup = Category::orderBy('name')->get()->groupBy('group');

        // Liczenie produktów dla KAŻDEJ kategorii przy aktualnie zaznaczonych filtrach
        $counts = [];

        foreach (Category::all() as $cat) {
            $counts[$cat->id] = Product::query()
                ->whereHas('categories', function ($q) use ($cat) {
                    $q->where('categories.id', $cat->id);
                })
                ->when(!empty($selectedCategories), function ($q) use ($selectedCategories, $cat) {
                    // zliczamy zgodnie z trybem AND, ale pomijamy bieżącą kategorię
                    foreach ($selectedCategories as $selectedId) {
                        if ($selectedId != $cat->id) {
                            $q->whereHas('categories', function ($qq) use ($selectedId) {
                                $qq->where('categories.id', $selectedId);
                            });
                        }
                    }
                })
                ->count();
        }

        return view('categories.show', [
            'category'          => $category,
            'products'          => $products,
            'categoriesByGroup' => $categoriesByGroup,
            'counts'            => $counts,
        ]);
    }
}
