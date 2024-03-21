const podcastPlay = async (event) => {
	if (!loading) {
		loading = true;
		const id = event.currentTarget.id;
		await playPodcast(id);
		loading = false;
	}
}

const playPodcast = async (id) => {
	if (currentTrack && currentTrack.uuid === id) {
		playPause();
	} else {
		let podcast = await getPodcast(id);
		if (podcast) {
			setPodcast(podcast);
			playPodcast();
		}
	}
}

const setPodcast = async (podcast) => {
	if (podcast?.id) {
		console.log("Now playing", podcast.id);
		currentTrack = podcast;
		audio.src = podcast.src;
	}
}

const getPodcast = async (id) => {
	if (id === null) return false;
	const response = await fetch(`/podcast/podcast/${id}`);
	const data = await response.json();
	if (data.success) {
		return data.data;
	}
	return false;
}
