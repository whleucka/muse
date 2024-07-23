<?php if ($tracks): ?>
<div class="table-responsive bg-app sticky-top">
	<div id="actions" class="my-2 d-flex">
		<button class="btn btn-sm btn-app me-2"
			hx-post="/search/reset"
			hx-indicator="#search .htmx-indicator"
			hx-select="#main"
			hx-target="#main"
			hx-swap="outerHTML">Reset</button>
		<button class="btn btn-sm btn-app"
			hx-get="/search/playlist/all"
			hx-indicator="#search .htmx-indicator"
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
