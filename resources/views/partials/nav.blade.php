<header>
    <div class="top-nav container">
    	<div class="top-nav-left">
	        <div class="logo"><a href="/">Ecommerce</a></div>

	        @if (! (request()->is('checkout') || request()->is('guestcheckout')))

	            {{-- This came from the admin panal  --}}
	            {{menu('main', 'partials.Menus.main')}}
	            
	        @endif
   		</div>
   		<div class="top-nav-right">
   			@if (! (request()->is('checkout') ||request()->is('guestcheckout')))
   			    {{-- This came from the admin panal  --}}
   			    @include('partials.Menus.main-right')
   			    
   			@endif
   		</div>
    </div> <!-- end top-nav -->
</header>
