const banner = `
░▒▓██████████████▓▒░░▒▓█▓▒░░▒▓█▓▒░░▒▓███████▓▒░▒▓████████▓▒░
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░      ░▒▓█▓▒░
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░░▒▓██████▓▒░░▒▓██████▓▒░
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░      ░▒▓█▓▒░▒▓█▓▒░
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░▒▓█▓▒░░▒▓█▓▒░      ░▒▓█▓▒░▒▓█▓▒░
░▒▓█▓▒░░▒▓█▓▒░░▒▓█▓▒░░▒▓██████▓▒░░▒▓███████▓▒░░▒▓████████▓▒░

 `;
console.log(banner);
const playerProgress = document.querySelector("#player-progress");
const preloadedBar = document.querySelector("#preload-progress");
const audio = document.querySelector("#audio");
const playIcon = `<i data-feather="play"></i>`;
const pauseIcon = `<i data-feather="pause"></i>`;
const defaultSkipTime = 10;
let playlist = [];
let index = 0;
let currentTrack = {};
let shuffleOn = true;
let repeatOn = true;
let loading = false;
let hls = new Hls();

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

const radioPlay = async (event) => {
	if (!loading) {
		loading = true;
		const uuid = event.currentTarget.id;
		await playRadio(uuid);
		loading = false;
	}
}

/**
 * When a track row is clicked
 * Used in search results
 */
const trackRowPlay = async (event) => {
	if (!loading) {
		loading = true;
		const uuid = event.currentTarget.id;
		await playUuid(uuid);
		loading = false;
	}
}

/**
 * When a playlist row is clicked
 * Used in playlist results
 */
const playlistRowPlay = async (event) => {
	if (!loading) {
		loading = true;
		const uuid = event.currentTarget.id;
		const playlist_index = event.currentTarget.dataset.playlist_index;
		await setPlaylistIndex(playlist_index);
		await playUuid(uuid);
		loading = false;
	}
}

const setPlaylistIndex = async (playlist_index) => {
	const payload = {
		"index": parseInt(playlist_index)
	}
	await postData("/playlist/index", payload);
	index = parseInt(playlist_index)
}

const getRadioStation = async (uuid) => {
	if (uuid === null) return false;
	// Get station info from API
	const response = await fetch(`/radio/station/${uuid}`);
	data = await response.json();
	if (data.success) {
		return data.data;
	}
	return false;
}

const playRadio = async (uuid) => {
	if (currentTrack && currentTrack.uuid === uuid) {
		playPause();
	} else {
		let station = await getRadioStation(uuid);
		if (station) {
			setRadioStation(station);
			playStation();
		}
	}
}

const playUuid = async (uuid) => {
	if (currentTrack && currentTrack.uuid === uuid) {
		playPause();
	} else {
		let track = await getTrack(uuid);
		if (track) {
			setTrack(track);
			playAudio();
		}
	}
}

const playTrack = async (track) => {
	if (currentTrack && currentTrack === track) {
		playPause();
	} else {
		setTrack(track);
		playAudio();
	}
}

const playlistTrack = async () => {
	const response = await fetch("/playlist/track");
	res = await response.json();
	if (res.success) {
		index = res.data.index;
		const uuid = res.data.track.uuid;
		if (uuid !== currentTrack?.uuid) {
			playTrack(res.data.track);
		}
	}
}

const getTrack = async (uuid) => {
	if (uuid === null) return false;
	// Get track info from API
	const response = await fetch(`/track/${uuid}`);
	data = await response.json();
	if (data.success) {
		return data.data;
	}
	return false;
}

const getPlaylistTrack = async (id) => {
	if (id === null) false;
	const response = await fetch(`/playlist/${id}`);
	data = await response.json();
	if (data.success) {
		return data.data;
	}
	return false;
}

const setTrack = async (track) => {
	if (track?.uuid) {
		console.log("Now playing", track.uuid);
		// Assign current track
		currentTrack = track;
		// Set the audio src
		audio.src = track.src;
	}
}

const setRadioStation = async (station) => {
	if (station?.uuid) {
		console.log("Now playing", station.uuid);
		// Assign current track
		currentTrack = station;
		// Set the audio src
		audio.src = station.src_url;
	}
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

const playStation = () => {
	if (Hls.isSupported()) {
		hls.attachMedia(audio);
		hls.on(Hls.Events.MEDIA_ATTACHED, (event, data) => {
			hls.loadSource(currentTrack.src);
			playAudio();
		});
	}
}

const pauseAudio = () => {
	if (!audio.paused) {
		// Pause audio
		playerProgress.classList.add("disabled");
		audio.pause();
		updatePlayPause();
	}
}

const seekForward = (event) => {
	// Seek playback foward
	const skipTime = event.seekOffset || defaultSkipTime;;
	const time = parseFloat(audio.currentTime) + parseFloat(skipTime);
	audio.currentTime = Math.min(time, audio.duration);
	updatePositionState();
}

const seekBackward = (event) => {
	// Seek playback backward
	const skipTime = event.seekOffset || defaultSkipTime;
	const time = parseFloat(audio.currentTime) - parseFloat(skipTime);
	audio.currentTime = Math.max(time, 0);
	updatePositionState();
}

const nextTrack = async () => {
	const nextTrackButton = document.querySelector(".btn.next");
	nextTrackButton.disabled = true;
	const response = await fetch("/player/next-track");
	res = await response.json();
	if (res.success) {
		index = res.data.index;
		await playTrack(res.data.track);
	}
	nextTrackButton.disabled = false;
}

const prevTrack = async () => {
	const prevTrackButton = document.querySelector(".btn.prev");
	prevTrackButton.disabled = true;
	const response = await fetch("/player/prev-track");
	res = await response.json();
	if (res.success) {
		index = res.data.index;
		await playTrack(res.data.track);
	}
	prevTrackButton.disabled = false;
}

const getShuffle = async () => {
	const shuffleBtn = document.querySelector(".btn.shuffle");
	const response = await fetch("/player/shuffle");
	res = await response.json();
	if (res.success) {
		shuffleOn = res.data;
		if (shuffleOn === 1) {
			shuffleBtn.classList.add("active");
		} else {
			shuffleBtn.classList.remove("active");
		}
	}
}

const shuffle = async () => {
	const shuffleBtn = document.querySelector(".btn.shuffle");
	shuffleBtn.disabled = true;
	const response = await fetch("/player/shuffle/toggle");
	res = await response.json();
	if (res.success) {
		shuffleOn = res.data;
		if (shuffleOn === 1) {
			shuffleBtn.classList.add("active");
		} else {
			shuffleBtn.classList.remove("active");
		}
	}
	shuffleBtn.disabled = false;
}

const getRepeat = async () => {
	const repeatBtn = document.querySelector(".btn.repeat");
	const response = await fetch("/player/repeat");
	res = await response.json();
	if (res.success) {
		repeatOn = res.data;
		if (repeatOn === 1) {
			repeatBtn.classList.add("active");
		} else {
			repeatBtn.classList.remove("active");
		}
	}
}

const repeat = async () => {
	const repeatBtn = document.querySelector(".btn.repeat");
	repeatBtn.disabled = true;
	const response = await fetch("/player/repeat/toggle");
	res = await response.json();
	if (res.success) {
		repeatOn = res.data;
		if (repeatOn === 1) {
			repeatBtn.classList.add("active");
		} else {
			repeatBtn.classList.remove("active");
		}
	}
	repeatBtn.disabled = false;
}

const updateTrackRow = () => {
	if (document.getElementById(currentTrack.uuid)) {
		const rows = document.querySelectorAll(".track-row");
		rows.forEach((el) => {
			el.classList.remove("active");
		});
		document.getElementById(currentTrack.uuid).focus();
		document.getElementById(currentTrack.uuid).classList.add("active");
	}
}

const updatePlayerCover = () => {
	const cover = document.querySelector("#player img");
	if (currentTrack) {
		cover.src = currentTrack.cover;
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

	updatePlayerCover();

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
	seekBackward(event);
});

navigator.mediaSession.setActionHandler('seekforward', function(event) {
	seekForward(event);
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
