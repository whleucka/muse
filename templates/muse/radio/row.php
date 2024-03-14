<div id="<?=$radio->uuid?>"
	tabindex="-1"
	class="track-row radio-row d-flex align-items-center w-100 px-1 truncate mt-2"
	onClick="radioPlay(event);">
	<img class="cover me-2"
		src="<?=$radio->cover_url?>"
		title="<?=$radio->name?>"
		alt="cover"
		loading="lazy" />
	<span><?=$radio->station_name?></span>
	<span class="mx-1">—</span>
	<span class="truncate pe-2"><?=$radio->location?></span>
	<span class="flex-grow-1 text-end"></span>
</div>


