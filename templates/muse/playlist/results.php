<?php if ($tracks): ?>
<div class="table-responsive">
	<div class="my-2 d-flex">
		<button class="btn btn-sm btn-danger"
			hx-get="/playlist/clear"
			hx-target="#main"
			hx-select="#main"
			hx-swap="outerHTML"
			>Reset</button>
	</div>
</div>
<script>
(function() {
	updateTrackRow();
})();
</script>
<?php endif ?>

<?php foreach ($tracks as $i => $track) : ?>
	<?=template("muse/playlist/row.php", ["track" => $track, "index" => $i])?>
<?php endforeach ?>

<?php if (!$tracks): ?>
	<p class="mt-4"><em>There are no tracks available</em></p>
<?php endif ?>

