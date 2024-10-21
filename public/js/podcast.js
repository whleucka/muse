    const podcastPlay = async (event) => {
        if (!loading) {
            showSpinner("#podcasts");
            loading = true;
            await playPodcast(event.currentTarget.dataset);
            loading = false;
            clearTrackRows();
            event.currentTarget.classList.add('active');
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
