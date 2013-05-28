@layout('layouts/main')

@section('body-props')
data-spy="scroll" data-target=".sidenav"
@endsection

@section('content')

<div class="row">
	<div id="sidenav" class="span3">

		<div class="well sidebar-nav sidenav">
			<ul id="categories" class="nav nav-list">
				<li class="nav-header">Categories</li>
				@foreach (array_keys($menu) as $cat)
					<li>
					@if ($cat == 'favorites')
						<a href="#{{ str_replace(' ', '_', $cat) }}"><i class="icon-star icon-blue"></i> {{ $cat }}</a>
					</li>
						<li class="divider"></li>
					@else
						<a href="#{{ str_replace(' ', '_', $cat) }}">{{ $cat }}</a>
					</li>
					@endif
				@endforeach
			</ul>
		</div>

	</div>
	<div id="menu" class="span9">
@parent
		<div id="menu-smallwindow"></div>
		@forelse ($menu as $cat => $items)
		<h1  id="{{ str_replace(' ', '_', $cat) }}">{{ ucwords($cat) }}</h1>
		<span class="divider"></span>
		<ul id="items" class="thumbnails">
			@forelse ($items as $name => $sku)
			<li class="span3">
				<div class="thumbnail">
					<img class="menuitem" id="thumb_{{ $name }}" src="{{ URL::to_asset('img/ktn.jpg') }}" alt="{{ $name }}" />
					<h3>{{ $name }}</h3>
					<p>Description</p>
					@foreach ($sku as $size => $price)
						<p class="item" data-toggle="tooltip" data-placement="top" title="Add this item to your cart."> 
							@if ($cat == 'favorites')
							<i class="icon-star favorite remove-favorite" id="{{ $name }}_{{ $size }}" data-toggle="tooltip" data-placement="top" title="Un-favorite this item."></i> 
							@else
								@if (Auth::check())
								<i class="icon-star-empty favorite add-favorite" id="{{ $name }}_{{ $size }}" data-toggle="tooltip" data-placement="top" title="Add this item to favorites."></i> 
								@else
								<i class="icon-star-empty favorite notloggedin" id="{{ $name }}_{{ $size }}" data-toggle="tooltip" data-placement="top" title="You must be logged in to add favorites."></i> 
								@endif
							@endif
							<span class="item" data-toggle="tooltip" data-placement="top" title="Add this item to your cart." data-item-size="{{ $size }}" data-item-price="{{ $price }}">({{ $size }}) ${{ $price }}</span>
						</p>
					@endforeach
				</div>
			</li>
			@empty
				</ul>
				@if ($cat == 'favorites')
					<div class="alert alert-info">
						<strong>You have no favorites!</strong>  Why not <a id="tooltip-favorite" href="#" data-toggle="tooltip" data-placement="right" title="To add favorites, click the heart icon on any menu item.">add some?</a>
					</div>
				@else
					<p>There are no items in this category.</p>
				@endif
			@endforelse
		</ul>
		@empty
			<p>The menu is empty.</p>
		@endforelse

	</div>
</div>
{{ Form::open(URL::to_action('menu@index'), 'POST', array('id' => 'favorite')) }}
	<input id="itemname" type="hidden" name="itemname"  value="none">
	<input type="hidden" name="form_action"  value="addfav">
{{ Form::close() }}
{{ Form::open(URL::to_action('menu@index'), 'POST', array('id' => 'unfavorite')) }}
	<input id="itemname" type="hidden" name="itemname"  value="none">
	<input type="hidden" name="form_action"  value="delfav">
{{ Form::close() }}
{{ Form::open(URL::to_action('menu@checkout'), 'POST', array('id' => 'checkout')) }}
{{ Form::close() }}
@endsection

@section('pagescripts')
<script>

	$(document).ready(function()
	{

		var addToCart = function(item, size, price)
		{
			if (cartIsEmpty)
				initCart();

			var i = item + size;

			if (i in cartArray)
			{
				cartArray[i]['quantity']++;
			}else{
				cartArray[i] = {
					'name': item,
					'size': size, 
					'price': price, 
					'quantity': 1
				};
			}

			redrawCart();
			saveCart();
		};

		var removeFromCart = function(item)
		{
			if (cartArray[item] != undefined)
			delete cartArray[item];

			//IE<8 compatibility hack
			if (!Object.keys) {
		    Object.keys = function (obj) {
		        var keys = [],
		            k;
		        for (k in obj) {
		            if (Object.prototype.hasOwnProperty.call(obj, k)) {
		                keys.push(k);
		            }
		        }
		        return keys;
		    };
		  }
		  if (Object.keys(cartArray).length == 0)
		  {
		   cartIsEmpty = true;
		  }
		  saveCart();
			redrawCart();
		}

		/* Insert cart markup and functionality
		 --------------------------------------*/
		var initCart = function()
		{
			cartIsEmpty = false;
			$('<ul/>', {
				class: 'nav navlist shopping-cart',
				id: 'shopping-cart-handle'
			}).appendTo('div.sidenav');
			$('#shopping-cart-handle').hide();
			$('<li/>', {
				class: 'divider shopping-cart'
			}).appendTo('ul#shopping-cart-handle');

			$('<li/>', {
				id: 'cart-header',
				class: 'nav-header shopping-cart',
				text: 'My Cart'
			}).appendTo('ul#shopping-cart-handle');

			$('<button/>', {
				id: 'close-cart',
				class: 'close shopping-cart',
				text: '×',
				type: 'button',
				'data-toggle': 'tooltip',
				title: 'Empty this cart.'
			}).appendTo('#cart-header');

			$('button#close-cart').tooltip();

			$('<ul/>', {
				id: 'cart-container',
				class: 'shopping-cart nav nav-list',
			}).appendTo('ul#shopping-cart-handle');

			$('button#close-cart').click(function()
			{
				uninitCart();
				unloadCart();
			});

			$('<div/>', {
				id: 'cart-items'
			}).appendTo('#cart-container');

			$('<div/>', {
				id: 'cart-totals'
			}).appendTo('#cart-container');

			checkWindow();
			$('#shopping-cart-handle').slideDown();
		}

		var checkWindow = function()
		{
			if ($(window).height() < 675 || $(window).width() < 980)
			{
				if ($('#shopping-cart-handle').parent() != $('#menu-smallwindow'))
				{
					var cart = $('#shopping-cart-handle').detach();
					$('#menu-smallwindow').append(cart);
					redrawCart();
				}
			}else{
				if ($('#shopping-cart-handle').parent() != $('div.sidenav'))
				{
					var cart = $('#shopping-cart-handle').detach();
					$('div.sidenav').append(cart);
					redrawCart();
				}
			}
		}

		var uninitCart = function()
		{
			$('#shopping-cart-handle').slideUp({
				complete: function()
				{
					cartIsEmpty = true;
					cartArray = {};
					$('.shopping-cart').remove();
				}
			});
		}

		var redrawCart = function()
		{

			if (cartIsEmpty)
			{
				unloadCart();
				uninitCart();
				return;
			}

			$('#cart-items').empty();
			$('#cart-totals').empty();
			$('button#cart-checkout').remove();

			var subtotal = 0;


			for (var item in cartArray)
			{
				var itemid = slug(item) + '_li';
				var price = (cartArray[item]['price'] * cartArray[item]['quantity']).toFixed(2);

				$('<li/>', {
					id: itemid,
					class: 'cart-item clearfix'
				}).appendTo('#cart-items');

				$('<span/>', {
					class: 'item-quantity',
					text: cartArray[item]['quantity']
				}).appendTo('#' + itemid);

				$('<span/>', {
					class: 'pull-left cart-item-name',
					id: itemid + '_name',
					text: cartArray[item]['name'] +  '(' + cartArray[item]['size'] + ')'
				}).appendTo('#' + itemid);

				$('<button/>', {
					type: 'button',
					class: 'close pull-left',
					id: 'rm_' + itemid,
					text: '×',
					'data-toggle': 'tooltip',
					'data-itemtorm': item,
					title: 'Remove this item from your cart.',
				}).prependTo('#' + itemid + '_name');

				$('#rm_' + itemid).tooltip(
				{
					placement: 'right'
				}).click(function()
				{
					removeFromCart($(this).attr('data-itemtorm'));
				});

				$('<span/>', {
					class: 'pull-right',
					text: price
				}).appendTo('#' + itemid);

				subtotal += parseFloat(price); 

			}

			var taxes = (subtotal * 0.05).toFixed(2);
			var total = (subtotal + parseFloat(taxes)).toFixed(2);


			$('<li/>', {
					class: 'divider'
				}).appendTo('#cart-totals');

			$('<li/>', {
					id: 'subtotal',
					class: 'cart-item clearfix'
				}).appendTo('#cart-totals');

			$('<span/>', {
					class: 'pull-left',
					text: 'Subtotal'
				}).appendTo('#subtotal');

				$('<span/>', {
					class: 'pull-right',
					text: '$' + subtotal
				}).appendTo('#subtotal');

			$('<li/>', {
					id: 'taxes',
					class: 'cart-item clearfix'
				}).appendTo('#cart-totals');

				$('<span/>', {
					class: 'pull-left',
					text: 'Tax (5%)'
				}).appendTo('#taxes');

				$('<span/>', {
					class: 'pull-right',
					text: '$' + taxes
				}).appendTo('#taxes');

			$('<li/>', {
				id: 'total',
				class: 'cart-item clearfix'
			}).appendTo('#cart-totals');

			$('<span/>', {
				class: 'pull-left',
				text: 'Total'
			}).appendTo('#total');

			$('<span/>', {
				class: 'pull-right',
				text: '$' + total
			}).appendTo('#total');

			$('<button/>', {
				id: 'cart-checkout',
				type: 'submit',
				class: 'btn btn-primary pull-right shopping-cart',
				text: 'Checkout'
			}).appendTo('ul#shopping-cart-handle');

			$('button#cart-checkout').click(function()
			{
				$('form#checkout').submit();
			});

			var max_cart_height = $(window).height() - ($('#cart-container').offset().top - $(window).scrollTop()) - ($('#cart-totals').height() + 70);

			var max_items_height = max_cart_height - $('#cart-container').height();

			$('#cart-items').css({
				'max-height': max_cart_height,
				'overflow': 'auto',
			});

			$('#cart-items').mCustomScrollbar({'theme': 'dark-thick'});
			$('#cart-items').mCustomScrollbar("scrollTo","last", {scrollInertia: 0});

			if ($('#cart-items').height() == max_cart_height)
			{
				$('#cart-items').css('margin-right', '-30px');
			}else{
				$('#cart-items').css('margin-right', '0px');
			}
		}

		//POST shopping cart to Session
		var saveCart = function()
		{
			$.ajax({
				url: 'menu/savecart',
				data: {cart: JSON.stringify(cartArray)},
				type: 'POST'
			});
		}

		//Overwrite POSTed shopping cart with an empty array
		var unloadCart = function()
		{
			$.ajax({
				url: 'menu/unsavecart',
				type: 'POST',
			});
		}

		var loadCart = function(data)
		{
			cartArray = JSON.parse(data);
			if (cartIsEmpty)
				initCart();
			redrawCart();
		}

		//Transform encoded text into slug format
		var slug = function(text)
		{
			text = text.replace(/^\s+|\s+$/g, ''); // trim
		  text = text.toLowerCase();

		  // remove accents, swap ñ for n, etc
		  var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:;";
		  var to   = "aaaaaeeeeeiiiiooooouuuunc------";
		  for (var i=0, l=from.length ; i<l ; i++) {
		    text = text.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
		  }

		  text = text.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
		    .replace(/\s+/g, '-') // collapse whitespace and replace by -
		    .replace(/-+/g, '-'); // collapse dashes

		  return text;
		};


		var cartIsEmpty = true;
		var cartArray = {};

		$('.sidenav').affix({
			'offset': {
				'top': function() { return $(window).width() <= 980 ? 40 : 0 }
			}
		});

		$('.sidenav a').click(function()
		{
			var href = $(this).attr('href');
			$('html, body').animate({

				scrollTop: $(href).offset().top
			}, 
			200,
			function()
			{
				window.location.hash = href;
			});
			return false;
		});

		$('#tooltip-favorite').tooltip();

		//add to favorite tooltip
		$('.favorite').tooltip();
		$('.add-favorite').click(function (e)
		{
			//stop from accidentally adding the item to the cart
			e.stopPropagation();
			$('form#favorite>input#itemname').val($(this).attr('id'));
			$('form#favorite').submit();
		});

		$('.remove-favorite').click(function (e)
		{
			//stop from accidentally adding the item to the cart
			e.stopPropagation();
			$('form#unfavorite>input#itemname').val($(this).attr('id'));
			$('form#unfavorite').submit();
		});

		//thumbnail tooltip
		$('span.item').tooltip();

		//thumbnail hover effects
		$('span.item').hover(function()
		{
			$(this).css({
				'color': '#08c',
				'cursor': 'pointer'
			});
		}, function()
		{
			$(this).css({
				'color': '#000',
				'cursor': 'auto'
			});
		});

		//thumbnail add to cart on click
		$('span.item').click(function()
		{
			addToCart($(this).parent().siblings('h3').text(), $(this).attr('data-item-size'), $(this).attr('data-item-price'));
		});

		var cart = ({{ $cart }});
		if (cart !== null && !$.isEmptyObject(JSON.parse(cart.cart)))
			loadCart(cart.cart);

		$(window).resize(function()
		{
			checkWindow();
		});


	});


</script>
@endsection