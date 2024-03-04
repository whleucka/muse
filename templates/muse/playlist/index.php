<section id="search" class="w-100">
	<h3 class="d-flex align-items-center">
		Playlist
		<span data-feather="list" class="ms-2"></span>
	</h3>
	<?php if ($playlist): ?>
	<div class="px-2 my-3 d-flex">
		<button class="btn btn-sm btn-outline-success"
			hx-get="/playlist/clear"
			hx-target="#main"
			hx-select="#main"
			hx-swap="outerHTML"
			><small>Reset</small></button>
	</div>
	<?php endif ?>
	<div id="tracks" class="my-2">
		<?php foreach ($playlist as $track): ?>
			<?=template("muse/tracks/row.php", ["track" => $track])?>
		<?php endforeach ?>

		<?php if (!$playlist): ?>
			<p class="mt-3"><em>There are no tracks available</em></p>
		<?php endif ?>
	</div>
</section>
<script>
feather.replace();
</script>
