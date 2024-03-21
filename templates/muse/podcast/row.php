<div id="<?=$podcast->id?>"
	tabindex="-1"
	class="track-row podcast-row d-flex align-items-center w-100 px-1 truncate mt-3"
	onClick="podcastPlay(event)">
	<img class="cover me-4"
		src="<?=$podcast->thumbnail?>"
		title="<?=$podcast->title_original?>"
		alt="cover"
		loading="lazy" />
	<div class="flex-grow-1 d-flex flex-column">
		<span class="podcast-name"><?=$podcast->podcast->title_original?></span>
		<span class="podcast-title"><?=$podcast->title_original?></span>
	</div>
</div>
