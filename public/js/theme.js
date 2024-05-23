const toggleDarkMode = (e) => {
	const checked = e.currentTarget.checked;
	if (checked) {
		darkModeOn();
		return;
	}
	darkModeOff();
}

const darkModeOn = () => {
	const _switch = document.querySelector("#dark-mode-switch");
	const tag = document.querySelector("#dark-mode");
	_switch.checked = true;
	tag.href = "/css/dark.css";
}

const darkModeOff = () => {
	const _switch = document.querySelector("#dark-mode-switch");
	const tag = document.querySelector("#dark-mode");
	_switch.checked = false;
	tag.href = "#";
}

/**
 * @return true if day, false if night
 */
const isDaytime = () => {
	const now = new Date(); // Get the current date and time
    const currentHour = now.getHours(); // Extract the hour from the current time

    // Day hours
    const dayStartHour = 9; // 9 AM
    const dayEndHour = 17; // 5 PM (24-hour format)

    // Check if the current hour falls within the day range
    return currentHour >= dayStartHour && currentHour < dayEndHour;
}

const isDay = isDaytime();
const dayOrNight = () => isDay ? darkModeOff() : darkModeOn();
console.log(isDay ? "Loading light theme." : "Loading dark theme.")
dayOrNight();
