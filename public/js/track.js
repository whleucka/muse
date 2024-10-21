const trackRowPlay = async (event) => {
    if (!loading) {
        showSpinner("#search");
        loading = true;
        await playTrack(event.currentTarget.dataset);
        loading = false;
    }
}

const playTrack = async (track) => {
    if (currentTrack && currentTrack === track) {
        playPause();
    } else {
        currentTrack = track;
        audio.src = track.src
        playAudio();
    }
}
