@layout('layouts/main')

@section('navigation')
@parent
<li><a href="about">About</a></li>
@endsection

@section('content')
<div class="hero-unit">
	<div class="row">
		<div class="span6">
			<h1>Dumplings</h1>
			<p>Here be a test.</p>
		</div>
		<div class="span4">
			<a href="#"><img src="http://placekitten.com/228/150" /></a>
		</div>
	</div>
</div>
@endsection