    let hls = new Hls();

    const radioPlay = async (event) => {
        if (!loading) {
            showSpinner("#radio");
            loading = true;
            await playRadio(event.currentTarget.dataset);
            loading = false;
        }
    }

    const playRadio = async (station) => {
        if (currentTrack && currentTrack === station) {
            playPause();
        } else {
            currentTrack = station;
            audio.src = station.src;
            playStation();
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
