<form method="POST" id="form-sign-in" hx-post="/auth/sign-in" hx-swap="outerHTML">
	<?=$csrf()?>
	<h1>Sign In</h1>
	<div>
		<label>Email</label><br>
		<input name="email" type="email" value="<?=$email?>" />
	</div>
	<div>
		<label>Password</label><br>
		<input name="password" type="password" value="" />
	</div>
	<div>
		<br>
		<button>Submit</button>
	</div>
</form>
