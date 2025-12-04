<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| PRODUKTY
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

/*
|--------------------------------------------------------------------------
| KATEGORIE
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CategoryController;

Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');

/*
|--------------------------------------------------------------------------
| KOSZYK
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CartController;

// wyświetlanie
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// dodawanie
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/add-weight/{product}', [CartController::class, 'addWithWeight'])->name('cart.addWeight');

// usuwanie
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// czyszczenie
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// zmiana ilości
Route::post('/cart/increase/{index}', [CartController::class, 'increase'])->name('cart.increase');
Route::post('/cart/decrease/{index}', [CartController::class, 'decrease'])->name('cart.decrease');

/*
|--------------------------------------------------------------------------
| CHECKOUT
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CheckoutController;

// AJAX – zapisywanie pól checkoutu
Route::post('/checkout/update-field', [CheckoutController::class, 'updateField'])
    ->name('checkout.updateField');

// zapis paczkomatu
Route::post('/checkout/save-locker', [CheckoutController::class, 'saveLocker'])
    ->name('checkout.shipping.point');

// podsumowanie zamówienia
Route::match(['get', 'post'], '/checkout/summary', [CheckoutController::class, 'summary'])
    ->name('checkout.summary');

// składanie zamówienia
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])
    ->name('checkout.placeOrder');

// sukces zamówienia
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])
    ->name('checkout.success');

// (opcjonalnie) placeholder kodów rabatowych, aby nie wywalało błędu
Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])
    ->name('checkout.coupon');


/*
|--------------------------------------------------------------------------
| STRONY STATYCZNE
|--------------------------------------------------------------------------
*/

Route::get('/o-nas', fn() => view('about-us.index'))->name('onas');
Route::get('/kontakt', fn() => view('contact.index'))->name('kontakt');
Route::get('/polityka-zwrotu', fn() => view('return-policy.index'))->name('returns');
Route::get('/polityka-prywatnosci', fn() => view('privacy-policy.index'))->name('privacy');
Route::get('/regulamin', fn() => view('regulamin.index'))->name('regulamin');

/*
|--------------------------------------------------------------------------
| COOKIES
|--------------------------------------------------------------------------
*/

Route::post('/accept-cookies', function () {
    return back()->cookie('cookies_accepted', true, 525600);
})->name('cookies.accept');
