<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');


/*
|--------------------------------------------------------------------------
| Produkty
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
// Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');


/*
|--------------------------------------------------------------------------
| Kategorie
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CategoryController;

Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');


/*
|--------------------------------------------------------------------------
| Koszyk
|--------------------------------------------------------------------------
*/

// use App\Http\Controllers\CartController;

// // Wyświetlanie koszyka
// Route::get('/cart', [CartController::class, 'show'])->name('cart.show');

// // Dodawanie (używane przez modal z gramaturą)
// Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');

// // Usuwanie produktu z koszyka
// Route::post('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');

// // Czyszczenie koszyka
// Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
use App\Http\Controllers\CartController;

// Koszyk – JEDNA, prosta wersja
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/add-weight/{product}', [CartController::class, 'addWithWeight'])->name('cart.addWeight');

Route::post('/cart/increase/{index}', [CartController::class, 'increase'])->name('cart.increase');
Route::post('/cart/decrease/{index}', [CartController::class, 'decrease'])->name('cart.decrease');

Route::get('/checkout', function () {
    return view('checkout.index');
})->name('checkout.index');

Route::get('/delivery', function () {
    return view('delivery.index');
})->name('delivery.index');



Route::get('/o-nas', function () {
    return view('about-us.index');
})->name('onas');


Route::get('/kontakt', function () {
    return view('contact.index');
})->name('kontakt');

Route::get('/polityka-zwrotu', function () {
    return view('return-policy.index');
})->name('returns');

Route::get('/polityka-prywatnosci', function () {
    return view('privacy-policy.index');
})->name('privacy');

Route::get('/regulamin', function () {
    return view('regulamin.index');
})->name('regulamin');


use App\Http\Controllers\CheckoutController;

Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');
Route::post('/update-shipping', [CheckoutController::class, 'updateShipping'])->name('checkout.shipping');
Route::post('/update-payment', [CheckoutController::class, 'updatePayment'])->name('checkout.payment');
// Route::get('/checkout-summary', [CheckoutController::class, 'summary'])->name('checkout.summary');

Route::post('/accept-cookies', function () {
    return back()->cookie('cookies_accepted', true, 60*24*365);
})->name('cookies.accept');


Route::get('/checkout', [CheckoutController::class, 'summary'])->name('checkout.summary');
// Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::post('/checkout/update-field', [CheckoutController::class, 'updateField'])
    ->name('checkout.updateField');

    Route::post('/checkout/shipping/point', [CheckoutController::class, 'saveLocker'])
    ->name('checkout.shipping.point');

    // Podsumowanie zamówienia
Route::get('/checkout/summary', [CheckoutController::class, 'summary'])
    ->name('checkout.summary');

// Złożenie zamówienia
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])
    ->name('checkout.placeOrder');

    Route::post('/checkout/save-locker', [CheckoutController::class, 'saveLocker'])
    ->name('checkout.shipping.point');
