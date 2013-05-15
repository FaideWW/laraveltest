@layout('layouts/main')

@section('content')
<div class="row">
	<div class="span3">
		<div class="well sidebar-nav">
			<ul class="nav nav-list">
				<li class="nav-header">Categories</li>
				<li @if ($active_cat == 'all') class="active" @endif>
					<a href="{{ $root_path }}menu/all">All</a>
				</li>
				@foreach ($cats as $cat)
					<li @if ($cat->id == $active_cat) class="active" @endif>
						<a href="{{ $root_path . 'menu/' . $cat->slug }}">{{ $cat->id }}</a>
					</li>
				@endforeach
			</ul>
		</div>
	</div>
	<div class="span9">
		@if ($redir != NULL)
			<div class="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				The category <strong>{{ $redir }}</strong> does not exist.
			</div>
		@endif
		<h1>{{ ucwords($active_cat) }}</h1>
		<ul class="thumbnails">
		@forelse ($menu as $name => $sku)
			<li class="span3">
				<div class="thumbnail">
					<img src="http://placekitten.com/400/400" alt="{{ $name }}" />
					<h3>{{ $name }}</h3>
					<p>Description</p>
					@foreach ($sku as $size => $price)
						<p>({{ $size }}) ${{ $price }}</p>
					@endforeach
				</div>
			</li>
		@empty
			</ul>
			<p>There are no items in this category.</p>
		@endforelse
		</ul>
	</div>
</div>
@endsection