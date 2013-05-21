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
		@forelse ($menu as $cat => $items)
		<h1  id="{{ str_replace(' ', '_', $cat) }}">{{ ucwords($cat) }}</h1>
		<span class="divider"></span>
		<ul id="items" class="thumbnails">
			@forelse ($items as $name => $sku)
			<li class="span3">
				<div class="thumbnail">
					<img class="hoverover" id="thumb_{{ $name }}" src="http://placekitten.com/400/400" alt="{{ $name }}" />
					<div class="contenthover">
						<button class="btn">Add to Cart</button>
						<i class="icon-shopping-cart pull-right"></i>
					</div>
					<h3>{{ $name }}</h3>
					<p>Description</p>
					@foreach ($sku as $size => $price)
						<p> 
							@if ($cat == 'favorites')
							<i class="icon-star remove-favorite" id="{{ $name }}_{{ $size }}" data-toggle="tooltip" data-placement="top" title="Un-favorite this item."></i> 
							@else
							<i class="icon-star-empty add-favorite" id="{{ $name }}_{{ $size }}" data-toggle="tooltip" data-placement="top" title="Add this item to favorites."></i> 
							@endif
							({{ $size }}) ${{ $price }}
						</p>
					@endforeach
				</div>
			</li>
			@empty
				</ul>
				@if ($cat == 'favorites')
					<p>You have no favorites!  Why not <a id="tooltip-favorite" href="#" data-toggle="tooltip" data-placement="right" title="To add favorites, click the heart icon on any menu item.">add some?</a></p>
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
{{ Form::open(URL::to_action('menu@addfav'), 'POST', array('id' => 'favorite')) }}
	<input type="hidden" name="itemname"  value="none">
{{ Form::close() }}
{{ Form::open(URL::to_action('menu@unfav'), 'POST', array('id' => 'unfavorite')) }}
	<input type="hidden" name="itemname"  value="none">
</form>
@endsection

@section('pagescripts')
<script>

	$(document).ready(function()
	{
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

		//menu item toolbar
		$('.hoverover').contenthover({
			overlay_background: '#000',
			overlay_opacity: 0.5,
			overlay_height: 40,
			overlay_y_position: 'top'
		});

		//add to favorite tooltip
		$('.add-favorite').tooltip();
		$('.add-favorite').click(function ()
		{
			$('form#favorite>input').val($(this).attr('id'));
			$('form#favorite').submit();
		});

		//unfavorite tooltip
		$('.remove-favorite').tooltip();
		$('.remove-favorite').click(function ()
		{
			$('form#unfavorite>input').val($(this).attr('id'));
			$('form#unfavorite').submit();
		});

	});


</script>
@endsection