let hls = new Hls();

const radioPlay = async (event) => {
	if (!loading) {
		loading = true;
		const uuid = event.currentTarget.id;
		await playRadio(uuid);
		loading = false;
	}
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

const getRadioStation = async (uuid) => {
	if (uuid === null) return false;
	const response = await fetch(`/radio/station/${uuid}`);
	data = await response.json();
	if (data.success) {
		return data.data;
	}
	return false;
}

const setRadioStation = async (station) => {
	if (station?.uuid) {
		console.log("Now playing", station.uuid);
		currentTrack = station;
		console.log(station);
		audio.src = station.src;
	}
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
