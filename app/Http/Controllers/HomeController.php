<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Sekcja NOWOŚCI – najnowsze produkty
        $newProducts = Product::orderBy('created_at', 'desc')->take(6)->get();

        // Sekcja NAJPOPULARNIEJSZE – na razie losowe
        $popularProducts = Product::inRandomOrder()->take(6)->get();

        return view('home.index', compact('newProducts', 'popularProducts'));
    }
}
