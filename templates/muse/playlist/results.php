<div class="table-responsive sticky-top bg-darkness">
	<div class="my-2 d-flex">
		<?php if ($tracks): ?>
		<button class="btn btn-sm btn-primary me-2"
			hx-get="/playlist/clear"
			hx-target="#main"
			hx-select="#main"
			hx-swap="outerHTML"
			hx-indicator="#playlist .htmx-indicator"
			>Reset</button>
		<?php endif ?>
		<button class="btn btn-sm btn-primary"
			hx-get="/playlist/random"
			hx-target="#main"
			hx-select="#main"
			hx-swap="outerHTML"
			hx-indicator="#playlist .htmx-indicator"
			>Random</button>
	</div>
</div>

<?php foreach ($tracks as $i => $track) : ?>
	<?=template("muse/playlist/row.php", ["track" => $track, "index" => $i])?>
<?php endforeach ?>

<?php if ($tracks): ?>
<script>
(function() {
	updateTrackRow();
})();
</script>
<?php else: ?>
	<p class="mt-2"><em>Playlist is empty</em></p>
<?php endif ?>

