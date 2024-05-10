feather.replace();

htmx.on("htmx:responseError", function(evt) {
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
