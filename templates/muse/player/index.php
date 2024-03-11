<section id="player" class="d-flex w-100">
	<img src="/img/no-album.png" alt="cover" class="cover" />
	<div id="controls" class="d-flex align-items-center w-100">
		<button class="btn prev" onClick="prevTrack()"><i data-feather="skip-back"></i></button>
		<button class="btn seek-backward" onClick="seekBackward()"><i data-feather="rewind"></i></button>
		<button class="btn play" onClick="playPause()"><i data-feather="play"></i></button>
		<button class="btn seek-forward" onClick="seekForward()"><i data-feather="fast-forward"></i></button>
		<button class="btn next" onClick="nextTrack()"><i data-feather="skip-forward"></i></button>
		<button class="btn shuffle <?=($shuffle ? 'active' : '')?>" onClick="shuffle()"><i data-feather="shuffle"></i></button>
		<button class="btn repeat <?=($repeat ? 'active' : '')?>" onClick="repeat()"><i data-feather="repeat"></i></button>
	</div>
</section>
<audio id="audio" />
