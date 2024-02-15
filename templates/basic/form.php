<form method="POST" action="/form/post">
	<?=$csrf()?>
	<div>
		<label>Name</label>
		<input type="text" name="name" value="" />
	</div>
	<div>
		<label>Age</label>
		<input type="tel" name="age" value="" />
	</div>
	<div>
		<button type="submit">Submit</button>
	</div>
</form>
