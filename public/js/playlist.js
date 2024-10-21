
    const playlistRowPlay = async (event) => {
        if (!loading) {
            showSpinner("#playlist");
            loading = true;
            await playPlaylist(event.currentTarget.dataset);
            loading = false;
        }
    }

    const playPlaylist = async (track) => {
        if (currentTrack && currentTrack === track) {
            playPause();
        } else {
            currentTrack = track;
            audio.src = track.src
            await setPlaylistIndex(track.playlist_index);
            playAudio();
        }
    }

    const setPlaylistIndex = async (playlist_index) => {
        const payload = {
            "index": parseInt(playlist_index)
        }
        await postData("/playlist/index", payload);
        index = parseInt(playlist_index)
    }
