<div class="table-responsive sticky-top bg-app">
	<div class="my-2 d-flex">
		<?php if ($tracks): ?>
		<button class="btn btn-sm btn-app me-2"
			hx-get="/playlist/clear"
			hx-target="#main"
			hx-select="#main"
			hx-swap="outerHTML"
			hx-indicator="#playlist .htmx-indicator"
			>Reset playlist</button>
		<?php else: ?>
		<button class="btn btn-sm btn-app"
			hx-get="/playlist/random"
			hx-target="#main"
			hx-select="#main"
			hx-swap="outerHTML"
			hx-indicator="#playlist .htmx-indicator"
			>Random Playlist</button>
		<button class="btn btn-sm btn-app ms-2" disabled>Most Played</button>
		<?php endif ?>
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

