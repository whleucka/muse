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

const getPlaylistTrack = async (id) => {
	if (id === null) false;
	const response = await fetch(`/playlist/${id}`);
	data = await response.json();
	if (data.success) {
		return data.data;
	}
	return false;
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
