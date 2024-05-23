// Feather icons
feather.replace();

// Auto close navbar when link is clicked
const navItems = document.querySelectorAll("#navbar .nav-item")
const menuToggle = document.getElementById("top-nav")
const bsCollapse = bootstrap.Collapse.getOrCreateInstance(menuToggle, {toggle: false})
navItems.forEach((l) => {
    l.addEventListener("click", () => {
        if (!menuToggle.classList.contains("collapsed")) {
            bsCollapse.hide()
        }
    })
})

const animate = (target) => {
	if (!target.disabled) {
		target.classList.add("active");
		setTimeout(() => {
			target.classList.remove("active");
		}, 200);
	}
}

const postData = async (endpoint, data) => {
	var formdata = new FormData();
	if (data) {
		for (const property in data) {
			formdata.append(property, data[property]);
		}
	}
	const response = await fetch(endpoint, {
		method: "POST",
		body: formdata,
		redirect: "follow"
	});
	return response.json();
}

const toggleDarkMode = (e) => {
	const checked = e.currentTarget.checked;
	if (checked) {
		darkModeOn();
		return;
	}
	darkModeOff();
}

const darkModeOn = () => {
	const tag = document.querySelector("#dark-mode");
	tag.href = "/css/dark.css";
}
// default dark mode on
darkModeOn();

const darkModeOff = () => {
	const tag = document.querySelector("#dark-mode");
	tag.href = "#";
}
