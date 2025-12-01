<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Lista produktów
    public function index()
    {
        $products = Product::with('categories')->get();
        return view('products.index', compact('products'));
    }

    // Formularz dodawania
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    // Zapis nowego produktu
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'origin'      => 'nullable|string|max:255',
            'ingredients' => 'nullable|string',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:4096',
            'image2'      => 'nullable|image|max:4096',
            'categories'   => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        // Obsługa zdjęcia
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        
        $image2Path = null;

        if ($request->hasFile('image2')) {
            $image2Path = $request->file('image2')->store('products', 'public');
        }


        // Tworzymy produkt
        $product = Product::create([
            'name'        => $request->name,
            'price'       => $request->price,
            'origin'      => $request->origin,
            'ingredients' => $request->ingredients,
            'description' => $request->description,
            'image'       => $imagePath,
        ]);

        // Przypisanie kategorii
        $product->categories()->attach($request->categories ?? []);

        return redirect()->route('products.index')->with('success', 'Produkt dodany!');
    }

    // Pojedynczy produkt
    public function show($id)
    {
        $product = Product::with('categories')->findOrFail($id);
        return view('products.show', compact('product'));
    }
}
