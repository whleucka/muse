<div id="<?=$track->uuid?>"
	tabindex="-1"
	class="track-row playlist-row d-flex align-items-center w-100 px-1 truncate mt-2"
	data-playlist_index="<?=$index?>"
	onClick="playlistRowPlay(event);">
	<img class="cover me-2"
		src="<?=$track->cover?>"
		title="<?=$track->album?>"
		alt="cover"
		loading="lazy" />
	<span><?=$track->artist?></span>
	<span class="mx-1">—</span>
	<span class="truncate pe-2"><?=$track->title?></span>
	<span class="flex-grow-1 text-end"><?=$track->playtime_string?></span>
</div>

