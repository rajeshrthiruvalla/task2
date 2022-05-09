<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShoppingCart;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[ShoppingCart::class, 'index'])->name('shopping_cart');

Route::get('cart', [ShoppingCart::class, 'cart'])->name('cart');
Route::get('add-to-cart/{id}', [ShoppingCart::class, 'addToCart'])->name('add.to.cart');
Auth::routes([ 'register' => false, 
  'reset' => false, 
  'verify' => false,
  'login'=>true,
    ]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('products', ProductController::class);
Route::get('get_datatable', [ProductController::class, 'getDataTable'])->name('get_datatable');