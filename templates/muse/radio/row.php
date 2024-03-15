<div id="<?=$radio->uuid?>"
	tabindex="-1"
	class="track-row radio-row d-flex align-items-center w-100 px-1 truncate mt-3"
	onClick="radioPlay(event);">
	<img class="cover me-4"
		src="<?=$radio->cover?>"
		title="<?=$radio->album?>"
		alt="cover"
		loading="lazy" />
	<div class="flex-grow-1 d-flex flex-column">
		<span class="station-name"><?=$radio->artist?></span>
		<em class="location"><?=$radio->title?></em>
	</div>
</div>


