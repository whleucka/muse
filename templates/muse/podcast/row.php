<div id="<?=$podcast->id?>"
	tabindex="-1"
	class="track-row podcast-row d-flex align-items-center w-100 px-1 truncate mt-3"
	onClick="podcastPlay(event)"
	data-title="<?=$podcast->title_original?>"
	data-artist="<?=$podcast->podcast->title_original?>"
	data-album="Muse Podcast"
	data-cover="<?=$podcast->thumbnail?>"
	data-src="<?=$podcast->audio?>"
>
	<img class="cover me-4"
		src="<?=$podcast->thumbnail?>"
		title="<?=$podcast->title_original?>"
		alt="cover"
		loading="lazy" />
	<div class="flex-grow-1 d-flex flex-column truncate">
		<span class="podcast-name truncate"><strong><?=$podcast->podcast->title_original?></strong></span>
		<span class="podcast-title truncate"><?=$podcast->title_original?></span>
		<span class="podcast-date"><small><?=date("Y-m-d", floor($podcast->pub_date_ms/1000))?></small></span>
	</div>
</div>
