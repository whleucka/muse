<form method="POST" id="form-register" hx-post="/register" hx-swap="outerHTML">
	<?=$csrf()?>
	<h1>Register</h1>
	<div>
		<label>Email</label><br>
		<input name="email" type="email" value="<?=$escape('email')?>" />
		<?= $request_errors("email") ?>
	</div>
	<div>
		<label>Name</label><br>
		<input name="name" type="text" value="<?=$escape('name')?>" />
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
	</div>
	<div>
		<br>
		<button type="submit">Submit</button>
	</div>
</form>
