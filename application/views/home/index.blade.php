@layout('layouts/main')


@section('content')
<div class="hero-unit">
	<div class="row">
		<div class="span6">
			<h1>Dumplings</h1>
			<p>Here be a test.</p>
		</div>
		<div class="span4">
			<form method="POST" class="well" action="user/authenticate">
				<fieldset>
					<legend>Login</legend>
					<input type="text" name="email" value="Email">
					<input type="password" name="password" value="********"><br />
					<label class="checkbox">
						<input type="checkbox" name="new_user">New to Dumplings?
					</label>
					<input type="submit" class="btn" value="Login">
				</fieldset>
			</form>
		</div>
	</div>
</div>
@endsection