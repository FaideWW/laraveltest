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
				<div class="thumbnail">
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
		$('.add-favorite').click(function ()
		{
			$('form#favorite>input#itemname').val($(this).attr('id'));
			$('form#favorite').submit();
		});

		$('.remove-favorite').click(function ()
		{
			$('form#unfavorite>input#itemname').val($(this).attr('id'));
			$('form#unfavorite').submit();
		});

	});


</script>
@endsection