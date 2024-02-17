<form method="POST" id="form-register" hx-post="/auth/register" hx-swap="outerHTML">
	<?=$csrf()?>
	<h1>Register</h1>
	<div>
		<label>Email</label><br>
		<input name="email" type="email" value="<?=$email?>" />
	</div>
	<div>
		<label>Name</label><br>
		<input name="name" type="text" value="<?=$name?>" />
	</div>
	<div>
		<label>Password</label><br>
		<input name="password" type="password" value="" />
	</div>
	<div>
		<label>Password (again)</label><br>
		<input name="password_match" type="password" value="" />
	</div>
	<div>
		<br>
		<button>Submit</button>
	</div>
</form>
