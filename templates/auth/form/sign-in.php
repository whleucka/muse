<form method="POST" id="form-sign-in" hx-post="/sign-in" hx-swap="outerHTML">
	<?= $csrf() ?>
	<h1>Sign In</h1>
	<div>
		<label>Email</label><br>
		<input name="email" type="email" value="<?= $escape('email') ?>" />
		<?= $request_errors('email') ?>
	</div>
	<div>
		<label>Password</label><br>
		<input name="password" type="password" value="" />
		<?= $request_errors('password') ?>
	</div>
	<div>
		<p>Don't have an account? <a hx-boost="true" href="/register" hx-select="main" hx-target="main">Create a new account</a></p>
	</div>
	<div>
		<button>Submit</button>
	</div>
</form>
