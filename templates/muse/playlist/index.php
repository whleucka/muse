<section id="search" class="w-100">
	<h3 class="d-flex align-items-center">
		Playlist
	</h3>
	<div id="tracks" class="my-2">
		<?php foreach ($playlist as $track): ?>
			<?=template("muse/tracks/row.php", ["track" => $track])?>
		<?php endforeach ?>
	</div>
</section>
