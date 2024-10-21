window.onbeforeunload = function() { return "The application will restart"; };

// Feather icons
feather.replace();

// Auto close navbar when link is clicked
const navItems = document.querySelectorAll("#navbar .nav-item")
const menuToggle = document.getElementById("top-nav")
const bsCollapse = bootstrap.Collapse.getOrCreateInstance(menuToggle, { toggle: false })
navItems.forEach((l) => {
    l.addEventListener("click", () => {
        if (!menuToggle.classList.contains("collapsed")) {
            bsCollapse.hide()
        }
    })
})

htmx.on("htmx:responseError", function (evt) {
    console.log("Oh snap! Response error!", evt.detail.xhr.status);
    switch (evt.detail.xhr.status) {
        case 404:
            console.log("Page not found!");
            window.location.href = "/page-not-found";
            break;
        case 403:
            console.log("Permission denied!");
            window.location.href = "/permission-denied";
            break;
        case 500:
            console.log("Server error!");
            window.location.href = "/server-error";
    }
});

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
