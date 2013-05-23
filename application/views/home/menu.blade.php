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
		@forelse ($menu as $cat => $items)
		<h1  id="{{ str_replace(' ', '_', $cat) }}">{{ ucwords($cat) }}</h1>
		<span class="divider"></span>
		<ul id="items" class="thumbnails">
			@forelse ($items as $name => $sku)
			<li class="span3">
				<div class="thumbnail" data-toggle="tooltip" data-placement="top" title="Add this item to your cart.">
					<img class="menuitem" id="thumb_{{ $name }}" src="{{ URL::to_asset('img/ktn.jpg') }}" alt="{{ $name }}" />
					<h3>{{ $name }}</h3>
					<p>Description</p>
					@foreach ($sku as $size => $price)
						<p> 
							@if ($cat == 'favorites')
							<i class="icon-star favorite remove-favorite" id="{{ $name }}_{{ $size }}" data-toggle="tooltip" data-placement="top" title="Un-favorite this item."></i> 
							@else
								@if (Auth::check())
								<i class="icon-star-empty favorite add-favorite" id="{{ $name }}_{{ $size }}" data-toggle="tooltip" data-placement="top" title="Add this item to favorites."></i> 
								@else
								<i class="icon-star-empty favorite notloggedin" id="{{ $name }}_{{ $size }}" data-toggle="tooltip" data-placement="top" title="You must be logged in to add favorites."></i> 
								@endif
							@endif
							({{ $size }}) ${{ $price }}
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
</form>
@endsection

@section('pagescripts')
<script>

	$(document).ready(function()
	{

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
			console.log('animate');
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
		$('.thumbnail').tooltip();

		//thumbnail hover effects
		$('.thumbnail').hover(function()
		{
			$(this).css({
				'border-color': '#08c',
				'cursor': 'pointer'
			});
		}, function()
		{
			$(this).css({
				'border-color': '#ddd',
				'cursor': 'auto'
			});
		});

		//thumbnail add to cart on click
		$('.thumbnail').click(function()
		{
			addToCart($(this).find('h3').text());
		});

		var addToCart = function(item)
		{
			if (cartIsEmpty)
				initCart();

			if (item in cartArray)
			{
				cartArray[item]++;
			}else{
				cartArray[item] = 1;
			}

			redrawCart();
		};

		/* Insert cart markup and functionality
		 --------------------------------------*/
		var initCart = function()
		{
			cartIsEmpty = false;
			$('<li/>', {
				class: 'divider shopping-cart'
			}).appendTo('ul#categories');

			$('<li/>', {
				id: 'cart-header',
				class: 'nav-header shopping-cart',
				text: 'My Cart'
			}).appendTo('ul#categories');

			$('<button/>', {
				id: 'close-cart',
				class: 'close shopping-cart',
				text: '×',
				type: 'button'
			}).appendTo('#cart-header');

			$('<div/>', {
				id: 'cart-container',
				class: 'shopping-cart',
			}).appendTo('div.sidenav');

			$('button#close-cart').click(function()
			{
				uninitCart();
			});
		}

		var uninitCart = function()
		{
			cartIsEmpty = true;
			cartArray = {};
			$('.shopping-cart').remove();
		}

		var redrawCart = function()
		{
			$('#cart-container').empty();

			for (var item in cartArray)
			{
				$('<form/>', {
					id: slug(item),
					class: 'form-inline cart-form'
				}).appendTo('#cart-container');

				$('<input/>', {
					id: slug(item) + '_q',
					type: 'text',
					class: 'input-tiny',
					value: cartArray[item]
				}).appendTo('form#' + slug(item));

				$('<span/>', {
					id: slug(item) + '_l',
					class: 'help-inline',
					text: item
				}).appendTo('form#' + slug(item));
			}
			console.log(JSON.stringify(cartArray));
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

	});


</script>
@endsection