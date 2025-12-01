<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    /**
     * Dodanie produktu z modala (waga albo sztuki)
     */
    public function addWithWeight(Request $request, Product $product)
    {
        // Czy produkt jest ZESTAWEM?
        $isSet = $product->categories->contains('id', 6);

        // Pobranie aktualnego koszyka
        $cart = session()->get('cart', []);

        /*
        |--------------------------------------------------------------------------
        | 1) PRODUKT ZESTAW
        |--------------------------------------------------------------------------
        */
        if ($isSet) {

            // ilość sztuk z formularza
            $quantity = max(1, (int) $request->input('set_quantity'));

            // sprawdzamy czy taki zestaw już istnieje
            $found = false;

            foreach ($cart as &$item) {
                if (
                    $item['product_id'] == $product->id &&
                    $item['type'] === 'set'
                ) {
                    // powiększamy ilość
                    $item['quantity'] += $quantity;
                    $item['price'] = $item['quantity'] * $item['price_per_unit'];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // nowy wpis
                $cart[] = [
                    'type'           => 'set',
                    'product_id'     => $product->id,
                    'name'           => $product->name,
                    'image'          => $product->image,
                    'quantity'       => $quantity,
                    'weight'         => null,
                    'price_per_unit' => $product->price,
                    'price'          => $product->price * $quantity,
                    'unit_label'     => 'zestaw',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2) PRODUKT NA WAGĘ (100g / 200g / własna waga)
        |--------------------------------------------------------------------------
        */
        else {

            $weight = $request->weight === 'custom'
                ? (int) $request->custom_weight
                : (int) $request->weight;

            $weight = max(1, $weight);

            // cena jednej paczki tej wagi
            $pricePerUnit = ($weight / 100) * $product->price;

            // szukamy, czy ta sama waga już istnieje
            $found = false;

            foreach ($cart as &$item) {
                if (
                    $item['product_id'] == $product->id &&
                    $item['type'] === 'weight' &&
                    $item['weight'] == $weight
                ) {
                    $item['quantity']++;
                    $item['price'] = $item['quantity'] * $item['price_per_unit'];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $cart[] = [
                    'type'           => 'weight',
                    'product_id'     => $product->id,
                    'name'           => $product->name,
                    'image'          => $product->image,
                    'quantity'       => 1,
                    'weight'         => $weight,
                    'price_per_unit' => $pricePerUnit,
                    'price'          => $pricePerUnit,
                    'unit_label'     => '100g',
                ];
            }
        }

        // zapis koszyka
        session()->put('cart', $cart);
        session()->flash('added_product', [
            'name' => $product->name,
            'image' => $product->image,
        ]);

        // przekierowanie
        return $request->goto === 'cart'
            ? redirect()->route('cart.index')->with('success', 'Dodano do koszyka!')
            : back()->with('success', 'Dodano do koszyka!');

    }

    /**
     * Usuwanie produktu po indeksie
     */
    public function remove($index)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Produkt usunięty.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Koszyk wyczyszczony.');
    }
    public function increase($index)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$index])) {
        $cart[$index]['quantity']++;

        // przeliczenie ceny końcowej
        $cart[$index]['price'] = $cart[$index]['quantity'] * $cart[$index]['price_per_unit'];
    }

    session()->put('cart', $cart);

    return back();
}

public function decrease($index)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$index])) {

        // nie schodzimy poniżej 1
        if ($cart[$index]['quantity'] > 1) {
            $cart[$index]['quantity']--;
            $cart[$index]['price'] = $cart[$index]['quantity'] * $cart[$index]['price_per_unit'];
        }
    }

    session()->put('cart', $cart);

    return back();
}

}
