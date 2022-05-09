<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class ShoppingCart extends Controller
{
    public function index() 
    {
//        \Illuminate\Support\Facades\Session::flush();
       $products= \App\Models\Product::all();
       return view('cart.product_list', compact('products'));
    }
     public function cart()

    {
        return view('cart.cart');
    }
     public function addToCart($id)

    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;

        } else {

            $cart[$id] = [

                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->ProductImages()->first()->name
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }
}
