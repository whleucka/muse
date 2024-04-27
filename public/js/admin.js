(function() {
	/**-------------------------- SIDEBAR CODE ------------------------------*/

	const sidebarFilter = document.querySelector('#sidebar #filter');
	sidebarFilter.oninput = (e) => {
		const value = e.currentTarget.value;
		resetHighlight();
		highlightMatches(value);
	};

	const highlightMatches = (text) => {
		if (text.trim() !== '') {
			animateLinks(text);
		} else {
			resetHighlight();
		}
	};

	const toggleSubmenu = (el, show = true) => {
		const submenu = el.closest(".submenu");
		const toggle_button = submenu.previousElementSibling;
		if (show) {
			submenu.classList.add("show");
		} else {
			submenu.classList.remove("show");
		}
		toggle_button.ariaExpanded = show;
	};

	const animateLinks = (text) => {
		const sidebarLinks = document.querySelectorAll('#sidebar .sidebar-link a');
		sidebarLinks.forEach((el, i) => {
			const regex = new RegExp(text, "gi");
			const found = el.dataset.title.match(regex);

			if (found) {
				var html = el.innerHTML;
				html = html.replace(regex, '<span class="highlight">$&</span>');
				el.innerHTML = html;

				toggleSubmenu(el, true);
			}
		});
	};

	const resetHighlight = () => {
		const sidebarLinks = document.querySelectorAll('#sidebar .sidebar-link a');
		sidebarLinks.forEach((el, i) => {
			const title = el.dataset.title;
			el.innerHTML = title;

			toggleSubmenu(el, false);
		});
	};
})();
