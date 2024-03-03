<div class="track-row d-flex align-items-center w-100 px-3 py-2"
	onClick="playTrack('<?=$track->uuid?>')">
	<img class="cover me-2"
		src="<?=$track->cover?>"
		title="<?=$track->album?>"
		alt="cover" />
	<span><?=$track->artist?></span>
	<span class="mx-1">—</span>
	<span><?=$track->title?></span>
	<span class="flex-grow-1 text-end"><?=$track->playtime_string?></span>
</div>

