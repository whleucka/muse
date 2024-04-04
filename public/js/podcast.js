const podcastPlay = async (event) => {
	if (!loading) {
		loading = true;
		await playPodcast(event.currentTarget.dataset);
		loading = false;
	}
}

const playPodcast = async (podcast) => {
	if (currentTrack && currentTrack === podcast) {
		playPause();
	} else {
		currentTrack = podcast;
		audio.src = podcast.src;
		playAudio();
	}
}
