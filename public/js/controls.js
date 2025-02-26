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
let shuffleOn = false;
let repeatOn = true;
let loading = false;

const showSpinner = (el = '') => {
    const spinner = document.querySelector(`${el} .htmx-indicator`);
    spinner.style.display = "block";
    spinner.style.opacity = 1.0;
}

const hideSpinner = (el = '') => {
    const spinner = document.querySelector(`${el} .htmx-indicator`);
    spinner.style.display = "none";
    spinner.style.opacity = 0.0;
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
    animate(document.querySelector(".btn.play"));
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

const pauseAudio = () => {
    if (!audio.paused) {
        // Pause audio
        playerProgress.classList.add("disabled");
        audio.pause();
        updatePlayPause();
    }
}

const seekForward = (event) => {
    if (audio.src === "") return;
    // Seek playback foward
    const seekForwardButton = document.querySelector(".btn.seek-forward");
    const skipTime = event.seekOffset || defaultSkipTime;;
    const time = parseFloat(audio.currentTime) + parseFloat(skipTime);
    audio.currentTime = Math.min(time, audio.duration);
    updatePositionState();
    animate(seekForwardButton);
}

const seekBackward = (event) => {
    if (audio.src === "") return;
    // Seek playback backward
    const seekBackwardButton = document.querySelector(".btn.seek-backward");
    const skipTime = event.seekOffset || defaultSkipTime;
    const time = parseFloat(audio.currentTime) - parseFloat(skipTime);
    audio.currentTime = Math.max(time, 0);
    updatePositionState();
    animate(seekBackwardButton);
}

const nextTrack = async () => {
    const nextTrackButton = document.querySelector(".btn.next");
    nextTrackButton.disabled = true;
    const response = await fetch("/player/next-track");
    res = await response.json();
    if (res.success && res.data) {
        index = res.data.index;
        await playTrack(res.data.track);
    }
    nextTrackButton.disabled = false;
    animate(nextTrackButton);
}

const prevTrack = async () => {
    const prevTrackButton = document.querySelector(".btn.prev");
    prevTrackButton.disabled = true;
    const response = await fetch("/player/prev-track");
    res = await response.json();
    if (res.success && res.data) {
        index = res.data.index;
        await playTrack(res.data.track);
    }
    prevTrackButton.disabled = false;
    animate(prevTrackButton);
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

const clearTrackRows = () => {
    const rows = document.querySelectorAll(".track-row");
    rows.forEach((el) => {
        el.classList.remove("active");
    });
}

const updateTrackRow = () => {
    if (document.getElementById(currentTrack.uuid)) {
        clearTrackRows();
        document.getElementById(currentTrack.uuid).focus();
        document.getElementById(currentTrack.uuid).classList.add("active");
    }
}

const updatePlayerCover = () => {
    const player_cover = document.querySelector("#player input[type='image']");
    const cover_popover = document.querySelector("#cover-popover img");
    if (currentTrack) {
        player_cover.src = currentTrack.cover;
        cover_popover.src = currentTrack.cover;
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

audio.addEventListener("play", function () {
    navigator.mediaSession.playbackState = 'playing';
    requestAnimationFrame(updateProgressBar);
    hideSpinner();
});

audio.addEventListener('pause', function () {
    navigator.mediaSession.playbackState = 'paused';
});

audio.addEventListener('ended', function () {
    nextTrack();
});

const updateProgressBar = () => {
    if (!audio.paused && !audio.ended) {
        // HAVE_ENOUGH_DATA prevents console errors
        if (audio.readyState === 4) {
            let preloaded = 0;
            if (audio.buffered.length > 0) {
                try {
                    preloaded = (audio.buffered.end(0) / audio.duration) * 100;
                } catch (e) {
                    console.warn("Buffered data not available:", e);
                }
            }
            const progress = (audio.currentTime / audio.duration) * 100;
            playerProgress.style.width = progress + "%";

            if (preloaded > 0) {
                preloadedBar.style.width = Math.max(preloaded - progress, 0) + "%";
            }
        }

        // Continue calling the function for the next frame
        requestAnimationFrame(updateProgressBar);
    }
}

navigator.mediaSession.setActionHandler('previoustrack', function () {
    prevTrack();
});

navigator.mediaSession.setActionHandler('nexttrack', function () {
    nextTrack();
});

navigator.mediaSession.setActionHandler('seekbackward', function (event) {
    seekBackward(event);
});

navigator.mediaSession.setActionHandler('seekforward', function (event) {
    seekForward(event);
});

navigator.mediaSession.setActionHandler('play', async function () {
    playAudio();
});

navigator.mediaSession.setActionHandler('pause', function () {
    pauseAudio();
});

try {
    navigator.mediaSession.setActionHandler('stop', function () {
        pauseAudio();
    });
} catch (error) {
    console.log('Warning! The "stop" media session action is not supported.');
}

try {
    navigator.mediaSession.setActionHandler('seekto', function (event) {
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

(function () {
    getShuffle();
})();
