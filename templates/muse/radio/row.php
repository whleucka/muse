<div id="<?=$radio->uuid?>"
	tabindex="-1"
	class="track-row radio-row d-flex align-items-center w-100 px-1 truncate mt-3"
	onClick="radioPlay(event);"
	data-uuid="<?=$radio->uuid?>"
	data-title="<?=$radio->station_name?>"
	data-artist="<?=$radio->location?>"
	data-album="Muse Radio"
	data-cover="<?=$radio->cover_url?>"
	data-src="<?=$radio->src_url?>"
>
	<img class="cover me-4"
		src="<?=$radio->cover_url?>"
		title="<?=$radio->station_name?>"
		alt="cover"
		loading="lazy" />
	<div class="flex-grow-1 d-flex flex-column">
		<span class="station-name"><?=$radio->station_name?></span>
		<em class="location"><?=$radio->location?></em>
	</div>
</div>
