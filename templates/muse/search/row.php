<div id="<?=$track->uuid?>"
	tabindex="-1"
	class="track-row search-row d-flex align-items-center w-100 px-1 mt-2">
    <div class="btn-group dropend">
        <button type="button" class="btn dropdown-toggle m-0 p-0 pe-2" data-bs-toggle="dropdown" aria-expanded="false">
            <img class="cover me-2"
                src="/cover/<?=$track->uuid?>/28/28"
                width="30"
                height="30"
                title="<?=$track->album?>"
                alt="cover"
                loading="lazy" />
        </button>
        <ul class="dropdown-menu">
            <a class="dropdown-item"
                onClick="trackRowPlay(event);"
                data-uuid="<?=$track->uuid?>"
                data-title="<?=$track->title?>"
                data-artist="<?=$track->artist?>"
                data-album="<?=$track->album?>"
                data-cover="<?=$track->cover?>"
                data-src="<?='/track/stream/'.$track->uuid?>"><li>Play track</li></a>
            <a class="dropdown-item" hx-get="/search/artist?term=<?=urlencode($track->artist)?>" hx-swap="none"><li>Search artist</li></a>
        </ul>
    </div>
	<span class="truncate"><?=$track->artist?></span>
	<span class="mx-1">—</span>
	<span class="truncate pe-2"><?=$track->title?></span>
	<span class="flex-grow-1 text-end"><?=$track->playtime_string?></span>
</div>
