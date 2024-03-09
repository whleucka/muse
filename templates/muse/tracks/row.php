<div class="track-row d-flex align-items-center w-100 py-2 px-1 truncate"
	data-uuid="<?=$track->uuid?>"
	<?php if ($index): ?>data-playlist_index="<?=$index?>"<?php endif ?>
	onClick="trackRowPlay(event);">
	<img class="cover me-2"
		src="<?=$track->cover?>"
		title="<?=$track->album?>"
		alt="cover"
		loading="lazy" />
	<span><?=$track->artist?></span>
	<span class="mx-1">—</span>
	<span class="truncate"><?=$track->title?></span>
	<span class="flex-grow-1 text-end"><?=$track->playtime_string?></span>
</div>

