<form method="POST" id="form-register" hx-post="/register" hx-swap="outerHTML">
	<?= $csrf() ?>
	<h1>Register</h1>
	<div id="email-input">
		<label>Email</label><br>
		<input name="email" type="email" value="<?= $escape('email') ?>" />
		<?= $request_errors("email") ?>
	</div>
	<div id="name-input">
		<label>Name</label><br>
		<input name="name" type="text" value="<?= $escape('name') ?>" />
		<?= $request_errors("name") ?>
	</div>
	<div>
		<label>Password</label><br>
		<input name="password" type="password" value="" />
		<?= $request_errors("password") ?>
	</div>
	<div>
		<label>Password (again)</label><br>
		<input name="password_match" type="password" value="" />
		<?= $request_errors("password_match", "Password") ?>
	</div>
	<div>
		<p><a hx-boost="true" href="/sign-in" hx-select="main" hx-target="main">        Already have an account?</a></p>
	</div>
	<div>
		<button type="submit">Submit</button>
	</div>
</form>
