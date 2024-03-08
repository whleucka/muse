<?php if ($tracks): ?>
<div class="table-responsive">
	<div class="my-3 d-flex">
		<button class="btn btn-sm btn-outline-success"
			hx-get="/playlist/clear"
			hx-target="#main"
			hx-select="#main"
			hx-swap="outerHTML"
			>Reset</button>
	</div>
</div>
<?php endif ?>

<?php foreach ($tracks as $i => $track) : ?>
	<?=template("muse/tracks/row.php", ["track" => $track, "index" => $i])?>
<?php endforeach ?>

<?php if (!$tracks): ?>
	<p class="mt-2"><em>There are no tracks available</em></p>
<?php endif ?>

