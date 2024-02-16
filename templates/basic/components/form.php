<div id="target"></div>
<form method="POST" hx-post="/form/post" hx-target="#target">
	<?=$csrf()?>
	<h2>Form</h2>
	<div>
		<strong>Name</strong><br>
		<input type="text" name="name" value="" />
	</div>
	<div>
		<strong>Age</strong><br>
		<input type="tel" name="age" value="" />
	</div>
	<div>
		<br>
		<button type="submit">Submit</button>
	</div>
</form>
