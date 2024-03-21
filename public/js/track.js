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
const setTrack = async (track) => {
	if (track?.uuid) {
		console.log("Now playing", track.uuid);
		// Assign current track
		currentTrack = track;
		// Set the audio src
		audio.src = track.src;
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
