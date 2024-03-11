const playerProgress = document.querySelector("#player-progress");
const preloadedBar = document.querySelector("#preload-progress");
const audio = document.querySelector("#audio");
const playIcon = `<i data-feather="play"></i>`;
const pauseIcon = `<i data-feather="pause"></i>`;
const defaultSkipTime = 10;
let playlist = [];
let index = 0;
let currentTrack = {};

const trackRowPlay = async (event) => {
	const uuid = event.currentTarget.id;
	const playlist_index = event.currentTarget.dataset.playlist_index;
	if (playlist_index !== '') {
		await setPlaylistIndex(playlist_index);
	}

	await playTrack(uuid);
}

const setPlaylistIndex = async (index) => {
	const payload = {
		"index": index
	}
	postData("/playlist/index", payload);
}

const playTrack = async (uuid) => {
	// Play track
	if (currentTrack && currentTrack.uuid === uuid) {
		playPause();
	} else {
		let track = await getTrack(uuid);
		await setTrack(track);
		playAudio();
	}
}

const postData = async (endpoint, data) => {
	var formdata = new FormData();
	if (data) {
		for (const property in data) {
			formdata.append(property, data[property]);
		}
	}
	const response = await fetch(endpoint, {
		method: 'POST',
		body: formdata,
		redirect: 'follow'
	});
	return response.json();
}

const load = async () => {
	const response = await fetch("/playlist/uuid");
	res = await response.json();
	if (res.success) {
		const uuid = res.data;
		if (uuid !== currentTrack?.uuid) {
			playTrack(uuid);
		}
	}
}

const getTrack = async (uuid) => {
	// Get track info from API
	const response = await fetch(`/track/${uuid}`);
	data = await response.json();
	if (data.success) {
		return data.data;
	}
	return false;
}

const getPlaylistTrack = async (id) => {
	const response = await fetch(`/playlist/${id}`);
	data = await response.json();
	if (data.success) {
		return data.data;
	}
	return false;
}

const setTrack = async (track) => {
	console.log("Now playing", track.uuid);
	// Assign current track
	currentTrack = track;
	// Set the audio src
	audio.src = track.src;
}

const updatePlayPause = () => {
	if (audio.src === "") return;
	// Update play/pause icon
	const play_btn = document.querySelector("#player #controls > .play");
	play_btn.innerHTML = audio.paused ? playIcon : pauseIcon;
	feather.replace();
}

const playPause = () => {
	// Play/Pause toggle
	if (audio.paused) {
		playAudio();
	} else {
		pauseAudio();
	}
	updatePlayPause();
}

const playAudio = () => {
	// Play audio
	audio.play()
		.then(_ => {
			playerProgress.classList.remove("disabled");
			updatePlayPause();
			updateMetadata();
		})
		.catch(error => console.log(error));
}

const pauseAudio = () => {
	// Pause audio
	playerProgress.classList.add("disabled");
	audio.pause();
	updatePlayPause();
}

const seekForward = () => {
	// Seek playback foward
	const skipTime = defaultSkipTime;
	const time = parseFloat(audio.currentTime) + parseFloat(skipTime);
	audio.currentTime = Math.min(time, audio.duration);
	updatePositionState();
}

const seekBackward = () => {
	// Seek playback backward
	const skipTime = defaultSkipTime;
	const time = parseFloat(audio.currentTime) - parseFloat(skipTime);
	audio.currentTime = Math.max(time, 0);
	updatePositionState();
}

const nextTrack = async () => {
	const nextTrackButton = document.querySelector(".btn.next");
	nextTrackButton.disabled = true;
	const response = await fetch("/playlist/next-track");
	uuid = await response.json();
	if (uuid && uuid !== 'end') {
		await playTrack(uuid);
	}
	nextTrackButton.disabled = false;
}

const prevTrack = async () => {
	const prevTrackButton = document.querySelector(".btn.prev");
	prevTrackButton.disabled = true;
	const response = await fetch("/playlist/prev-track");
	uuid = await response.json();
	if (uuid && uuid !== 'end') {
		await playTrack(uuid);
	}
	prevTrackButton.disabled = false;
}

const shuffle = () => {
	console.log("wip");
}

const repeat = () => {
	console.log("wip");
}

const updateTrackRow = () => {
	if (audio.src && document.getElementById(currentTrack.uuid)) {
		const rows = document.querySelectorAll(".track-row");
		rows.forEach((el) => {
			el.classList.remove("active");
		});
		document.getElementById(currentTrack.uuid).focus();
		document.getElementById(currentTrack.uuid).classList.add("active");
	}
}

const updateMetadata = () => {
	// Set the mediaSession
	navigator.mediaSession.metadata = new MediaMetadata({
		title: currentTrack.title,
		artist: currentTrack.artist,
		album: currentTrack.album,
		artwork: [
			{ src: currentTrack.cover, sizes: '256x256', type: 'image/png' },
		]
	});

	updateTrackRow();

	updatePositionState();
}

const updatePositionState = () => {
	if ('setPositionState' in navigator.mediaSession) {
		navigator.mediaSession.setPositionState({
			duration: audio.duration,
			playbackRate: audio.playbackRate,
			position: audio.currentTime
		});
	}
}

audio.addEventListener("play", function() {
	navigator.mediaSession.playbackState = 'playing';
});

audio.addEventListener('pause', function() {
	navigator.mediaSession.playbackState = 'paused';
});

audio.addEventListener('ended', function() {
	nextTrack();
});

audio.addEventListener("timeupdate", function() {
	// HAVE_ENOUGH_DATA, prevents console errors
	if (audio.readyState === 4) {
		const preloaded = (audio.buffered.end(0) / audio.duration) * 100;
		const progress = (audio.currentTime / audio.duration) * 100;
		playerProgress.style.width = progress + "%";
		preloadedBar.style.width = preloaded - progress + "%";
	}
});

navigator.mediaSession.setActionHandler('previoustrack', function() {
	prevTrack();
});

navigator.mediaSession.setActionHandler('nexttrack', function() {
	nextTrack();
});

navigator.mediaSession.setActionHandler('seekbackward', function(event) {
	seekBackward();
});

navigator.mediaSession.setActionHandler('seekforward', function(event) {
	seekForward();
});

navigator.mediaSession.setActionHandler('play', async function() {
	playAudio();
});

navigator.mediaSession.setActionHandler('pause', function() {
	pauseAudio();
});

try {
	navigator.mediaSession.setActionHandler('stop', function() {
		pauseAudio();
	});
} catch (error) {
	console.log('Warning! The "stop" media session action is not supported.');
}

try {
	navigator.mediaSession.setActionHandler('seekto', function(event) {
		if (event.fastSeek && ('fastSeek' in audio)) {
			audio.fastSeek(event.seekTime);
			return;
		}
		audio.currentTime = event.seekTime;
		updatePositionState();
	});
} catch (error) {
	console.log('Warning! The "seekto" media session action is not supported.');
}

load();
