<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
class SaveForLaterController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Cart::instance('saveForLater')->remove($id);

        return back()->with('success_message','Item has been removed');
    }

    public function switchToCart($id)
    {
        $item = Cart::instance('saveForLater')->get($id);

        Cart::instance('saveForLater')->remove($id);

        $dublicate = Cart::instance('default')->search(function($cartItem,$rowId) use ($id){
            return $rowId === $id;
        });

        if($dublicate->isNotEmpty()){
            return redirect()->route('cart.index')->with('success_message','Item is alerade in your cart');
        }

        Cart::instance('default')->add($item->id,$item->name,1,$item->price)->associate('App\Product');

        return redirect()->route('cart.index')->with('success_message', 'Item has been added to your cart!');
    }
}
