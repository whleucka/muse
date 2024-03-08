<?php if ($tracks): ?>
<div class="table-responsive">
	<div class="my-3 d-flex">
		<button class="btn btn-sm btn-outline-success"
			hx-get="/playlist/set"
			hx-swap="none">Add to playlist</button>
	</div>
</div>
<?php endif ?>

<?php foreach ($tracks as $i => $track) : ?>
	<?=template("muse/tracks/row.php", ["track" => $track, "index" => false])?>
<?php endforeach ?>

<?php if (!$tracks): ?>
	<p class="mt-2"><em>Sorry, no tracks could be found</em></p>
<?php endif ?>
