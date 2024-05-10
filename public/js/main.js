feather.replace();

htmx.on("htmx:responseError", function(evt) {
	console.log("Oh snap! Response error!", evt.detail.xhr.status);
});
