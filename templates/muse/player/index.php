<section id="player" class="d-flex w-100">
	<img src="/img/no-album.png" alt="cover" class="cover" />
	<div id="controls" class="d-flex align-items-center w-100">
		<button class="btn prev" onClick="prev()"><i data-feather="skip-back"></i></button>
		<button class="btn seek-backward" onClick="seekBackward()"><i data-feather="rewind"></i></button>
		<button class="btn play" onClick="playPause()"><i data-feather="play"></i></button>
		<button class="btn seek-forward" onClick="seekForward()"><i data-feather="fast-forward"></i></button>
		<button class="btn next" onClick="next()"><i data-feather="skip-forward"></i></button>
		<div class="progress w-100 me-3">
			<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="player-progress"></div>
			<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="preload-progress"></div>
		</div>
	</div>
</section>
<audio id="audio" />
