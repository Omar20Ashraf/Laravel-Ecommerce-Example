<?php
use Gloudemans\Shoppingcart\Facades\Cart;
Route::get('empty',function(){
	Cart::destroy();
});


Route::get('/', 'LandingPageController@index')->name('landing-page');

//Shop pages routes
Route::get('/shop', 'ShopController@index')->name('shop.index');
Route::get('/shop/{product}', 'ShopController@show')->name('shop.show');

//Cart Routes
Route::get('/cart', 'CartController@index')->name('cart.index');
Route::post('/cart', 'CartController@store')->name('cart.store');
Route::patch('/cart/{product}', 'CartController@update')->name('cart.update');
Route::delete('/cart/{product}', 'CartController@destroy')->name('cart.destroy');
Route::post('/cart/switchToSaveForLater/{product}', 'CartController@switchToSaveForLater')->name('cart.switchToSaveForLaters');

//Save for later Routes
Route::delete('/saveForLater/{product}', 'SaveForLaterController@destroy')->name('saveForLater.destroy');
Route::post('/saveForLater/switchToSaveForLater/{product}', 'SaveForLaterController@switchToCart')->name('saveForLater.switchToCart');

//CheckOut Page Controller
Route::get('/checkout', 'CheckOutController@index')->name('checkout.index')->middleware('auth');
Route::post('/checkout', 'CheckOutController@store')->name('checkout.store');

// Route::post('/paypal-checkout', 'CheckOutController@paypalCheckout')->name('checkout.paypal');

//guest checout
Route::get('/guestcheckout', 'CheckOutController@index')->name('guestcheckout.index');

//Coupons Ctrl
Route::post('/coupons', 'CouponsCtrl@store')->name('coupons.store');

Route::delete('/coupons', 'CouponsCtrl@destroy')->name('coupons.destroy');

Route::get('/search', 'ShopController@search')->name('search');





Route::get('/thankyou', 'ConfrimationCtrl@index')->name('Confirmation.index');




Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('auth')->group( function (){

	Route::get('/my-profile', 'UsersCtrl@edit')->name('users.edit');
	Route::patch('/my-profile', 'UsersCtrl@update')->name('users.update');

	Route::get('/my-orders', 'OrdersCtrl@index')->name('orders.index');
	Route::get('/my-orders/{order}', 'OrdersCtrl@show')->name('orders.show');

});
