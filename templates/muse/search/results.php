<?php if ($tracks): ?>
<div class="table-responsive">
	<div class="my-3 d-flex">
		<button class="btn btn-sm btn-success"
			hx-get="/playlist/set"
			hx-swap="none">Play all</button>
	</div>
</div>
<script>
(function() {
	updateTrackRow();
})();
</script>
<?php endif ?>

<?php foreach ($tracks as $i => $track) : ?>
	<?=template("muse/search/row.php", ["track" => $track])?>
<?php endforeach ?>

<?php if (!$tracks): ?>
	<p class="mt-2"><em>Sorry, no tracks could be found</em></p>
<?php endif ?>
