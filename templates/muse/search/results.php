<?php if ($tracks): ?>
<div class="px-2 my-3 d-flex">
	<button class="btn btn-sm btn-outline-success"
		hx-get="/search/playlist"
		hx-swap="none">Add to playlist</button>
</div>
<?php endif ?>

<?php foreach ($tracks as $track) : ?>
	<?=template("muse/tracks/row.php", ["track" => $track])?>
<?php endforeach ?>

<?php if (!$tracks): ?>
	<p class="mt-2"><em>Sorry, no tracks could be found</em></p>
<?php endif ?>
