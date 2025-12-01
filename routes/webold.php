<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/', function () {
//     return redirect('/products');
// });

// Lista produktów
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Formularz dodawania produktu
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

// Zapis nowego produktu
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

// Pojedynczy produkt
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

///////////////////////////////////////////////////////////////////////////////////////

use App\Http\Controllers\CartController;

// Koszyk – wyświetlanie
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Dodawanie do koszyka
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');

// Usuwanie pojedynczego produktu
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// Czyszczenie całego koszyka
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

///////////////////////////////////////////////////////////////////////////////////////

use App\Http\Controllers\CategoryController;

Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');

// Strona koszyka
Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'show'])->name('cart.show');



