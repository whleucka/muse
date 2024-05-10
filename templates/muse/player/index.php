<section id="player" class="d-flex w-100">
	<div id="cover-popover" popover>
		<img src="/img/no-album.png" alt="cover" class="full-cover" />
	</div>
	<input popovertarget="cover-popover" type="image" src="/img/no-album.png" class="cover" />
	<div id="controls" class="d-flex align-items-center justify-content-evenly justify-content-sm-start w-100">
		<button class="btn prev" onClick="prevTrack()"><i data-feather="skip-back"></i></button>
		<button class="btn seek-backward" onClick="seekBackward(event)"><i data-feather="rewind"></i></button>
		<button class="btn play" onClick="playPause()"><i data-feather="play"></i></button>
		<button class="btn seek-forward" onClick="seekForward(event)"><i data-feather="fast-forward"></i></button>
		<button class="btn next" onClick="nextTrack()"><i data-feather="skip-forward"></i></button>
		<button class="btn shuffle <?=($shuffle ? 'active' : '')?>" onClick="shuffle()"><i data-feather="shuffle"></i></button>
		<!--<button class="btn repeat <?=($repeat ? 'active' : '')?>" onClick="repeat()"><i data-feather="repeat"></i></button>-->
	</div>
</section>
<audio id="audio" />
