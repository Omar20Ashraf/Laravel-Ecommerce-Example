<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories= Category::all();
        $pagination = 9;
        if(request()->category){
            $products = Product::with('categories')->whereHas('categories',function($query)
            {
                $query->where('slug',request()->category);
            });
            $categoryName = optional($categories->where('slug',request()->category)->first())->name;

        } else{     
            $products = Product::where('featured',false);
            $categoryName = 'Featured';
        }

        if(request()->sort == 'low_high'){
            $products = $products->orderBy('price')->paginate($pagination);

        } elseif(request()->sort == 'high_low'){
            $products = $products->orderBy('price','desc')->paginate($pagination);      
        } else{
            $products = $products->paginate($pagination);
        }

        return view('shop',compact('categories','products','categoryName'));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $mightAlsoLike = Product::where('slug', '!=', $slug)->mightAlsoLike()->get();
        
        $stockLevel = getStockLevel($product->quantity);    

        return view('product')->with([
            'product' => $product,
            'mightAlsoLike' => $mightAlsoLike,
            'stockLevel' => $stockLevel
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|min:3'
        ]);
        $query = $request->input('query');

        $products = Product::where('name', 'like', "%$query%")
                            ->orWhere('details', 'like', "%$query%")
                            ->orWhere('description', 'like', "%$query%")
                            ->paginate(10);

        return view('search-results',compact('products'));
    }
}
