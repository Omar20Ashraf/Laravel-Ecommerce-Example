<?php

// Lesson -2
Model ::inRandomOrder()->take(num)->get();
///////////////////////////////////////////////////////////////////////////
// lesson-4
// to prevent submitting the form Twice
// in JavaScript
	//in checkout.blade
	1-document.getElementById('complete-order').disabled=true;
	// put it in the submit fun after preventDefault()
	2-give the submit button the id 
	3-repeat 1 in error section and make it false
////////////////////////////////////////////////////////////////////////
//Lesson - 5 update the quantity

use axios : it came installed with laravel
use the code in the axios Package in github

////////////////////////////////////////////////////////////////////////
//Lesson - 6
//when making seed define the relationship in thr seed page between the tables if the relation exist by using the fun name [exist] in the model.

//you can use query statement with:
	 {{ route('r.r',['dbItem' => $foreach variable->[] ]) }}
	 //you dont have to make new route

// fun:optional() check if its no

///////////////////////////////////////////////////////
//lesson -7 Coupons

// watch the polymorphism video in the dscription	 
session()->put('name',['var1'=>$var1]);
session()->forget('name')
session()->get('name')['value']

return collect([
	'var1'=>$var,
]); instead of return theArray
to get the values fun name()->get('name');
