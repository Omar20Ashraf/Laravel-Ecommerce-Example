<?php

namespace App\Http\Controllers;

// use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\Http\Requests\CheckoutRequest;
use App\Mail\OrderPlaced;
use App\Order;
use App\OrderProduct;
use App\Product;
use Cartalyst\Stripe\Exception\CardErrorException;
use Cartalyst\Stripe\Stripe;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class CheckOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Cart::instance('default')->count() == 0) {
            return redirect()->route('shop.index');
        }

        if (auth()->user() && request()->is('guestcheckout')) {
            return redirect()->route('checkout.index');
        }

        $discount =     getNumbers()->get('discount');
        $newSubtotal =  getNumbers()->get('newSubtotal');
        $newTax =       getNumbers()->get('newTax'); 
        $newTotal =     getNumbers()->get('newTotal'); 
        return view('checkout',compact('discount','newSubtotal','newTax','newTotal'));
    }

    public function store(CheckoutRequest $request)
    {
        // Check race condition when there are less items available to purchase
        if ($this->productsAreNoLongerAvailable()) {
            return back()->withErrors('Sorry! One of the items in your cart is no longer avialble.');
        }
        
        $contents = Cart::content()->map(function ($item) {
            return $item->model->slug.', '.$item->qty;
        })->values()->toJson();
        try {
           $stripe = Stripe::make('sk_test_LeleRLuM2G48ngSua2eMoXdP');
           $token = $_POST['stripeToken'];
           $charge = $stripe->charges()->create([
               'amount'   => getNumbers()->get('newTotal'),
               'currency' => 'USD',
               'source' => $request->stripeToken,
               'description' =>'order',
               'receipt_email' => $request->email,
               'metadata' => [
                    'contents' => $contents,
                    'quantity' => Cart::instance('default')->count(),
                    'discount' => collect(session()->get('coupon'))->toJson(),
                ],

           ]);

            //Insert into order table

            $order = $this->addToOrdersTable($request,null);

            $this->decreaseQuantities();

             Mail::send(new OrderPlaced($order));
           //empty the card after payment
           Cart::instance('default')->destroy();
            session()->forget('coupon');
           //success
           return redirect()->route('Confirmation.index')->with('success_message', 'Thank you! Your payment has been successfully accepted!');
 
        } catch (CardErrorException $e) {
            $this->addToOrdersTable($request,$e->getMessage());
            return back()->withErrors('Error! ' . $e->getMessage());
        }
    }
    protected function addToOrdersTable($request,$error)
    {
        $order = Order::create([
            'user_id' => auth()->user() ? auth()->user()->id : null,
            'billing_email'         => $request->email,
            'billing_name'          => $request->name,
            'billing_address'       => $request->address,
            'billing_city'          => $request->city,
            'billing_province'      => $request->province,
            'billing_postalcode'    => $request->postalcode,
            'billing_phone'         => $request->phone,
            'billing_name_on_card'  => $request->name_on_card,
            'billing_discount'      => getNumbers()->get('discount'),
            'billing_discount_code' => getNumbers()->get('code'),
            'billing_subtotal'      => getNumbers()->get('newSubtotal'),
            'billing_tax'           => getNumbers()->get('newTax'),
            'billing_total'         => getNumbers()->get('newTotal'),
            'error'                 => $error,

        ]);

        //insert into order_Product
        foreach (Cart::content() as $item) {
             OrderProduct::create([
                 'order_id' => $order->id,//came from insert the data into order table
                 'product_id' => $item->model->id,
                 'quantity' => $item->qty,
             ]);
         } 

        return $order;
    }

    protected function decreaseQuantities()
    {
        foreach (Cart::content() as $item) {
            $product = Product::find($item->model->id);

            $product->update(['quantity' => $product->quantity - $item->qty]);
        }
    }

    protected function productsAreNoLongerAvailable()
    {
        foreach (Cart::content() as $item) {
            $product = Product::find($item->model->id);
            if ($product->quantity < $item->qty) {
                return true;
            }
        }

        return false;
    }
}
