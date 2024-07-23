<?php if ($podcasts): ?>
<div class="table-responsive sticky-top bg-app">
	<div class="my-2 d-flex">
		<button class="btn btn-sm btn-app me-2"
			hx-get="/podcast/reset"
			hx-target="#main"
			hx-select="#main"
			hx-swap="outerHTML"
			hx-indicator="#playlist .htmx-indicator"
			>Reset</button>
	</div>
</div>
<?php endif ?>

<?php foreach ($podcasts as $i => $podcast) : ?>
	<?=template("muse/podcast/row.php", ["podcast" => (object)$podcast])?>
<?php endforeach ?>

<?php if (!$podcasts): ?>
	<p class="mt-2"><em>Sorry, no podcasts could be found</em></p>
<?php endif ?>
