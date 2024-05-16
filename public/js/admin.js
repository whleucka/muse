/** Form events */
const handleCheck = (e) => {
	const self = e.currentTarget;
	const hidden = self.previousElementSibling;
	hidden.value = self.checked ? 1 : 0;
	console.log(hidden);
}

(function() {
	document.addEventListener("keydown", (e) => {
		const filter = document.querySelector("#sidebar #filter");
		if (e.ctrlKey) {
			switch (e.which) {
				// This is ctrl + / key
				case 191:
					filter.focus();
					setTimeout(() => {filter.value = ''}, 0);
					break;
			}
		}
	})
	/**-------------------------- NAVBAR CODE  ------------------------------*/
	const menu = document.querySelector("#toggle-sidebar");
	menu.addEventListener("click", (e) => {
		const sidebar = document.querySelector("#sidebar");
		if (sidebar.classList.contains("d-none")) {
			sidebar.classList.remove("d-none");
		} else {
			sidebar.classList.add("d-none");
		}
	})

	/**-------------------------- SIDEBAR CODE ------------------------------*/

	const sidebarFilter = document.querySelector('#sidebar #filter');
	if (sidebarFilter !== null) {
		sidebarFilter.oninput = (e) => {
			const value = e.currentTarget.value;
			resetHighlight();
			highlightMatches(value);
		};
	}

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

	const hide = (el) => {
		el.style.display = "none";
	}

	const show = (el, type = "block") => {
		el.style.display = type;
	}

	const hideParentLinks = () => {
		const parentLinks = document.querySelectorAll("#sidebar .parent-link");
		parentLinks.forEach((el,i) => {
			if (el.ariaExpanded == "true") {
				show(el, "flex");
			} else {
				hide(el);
			}
		});
	}

	const showParentLinks = () => {
		const parentLinks = document.querySelectorAll("#sidebar .parent-link");
		parentLinks.forEach((el,i) => {
			show(el, "flex");
		});
	}

	const animateLinks = (text) => {
		const sidebarLinks = document.querySelectorAll('#sidebar .sidebar-link a');
		sidebarLinks.forEach((el, i) => {
			const regex = new RegExp(text, "gi");
			const found_title = el.dataset.title.match(regex);
			const found_parent = el.dataset.parent.match(regex);

			if (found_title || found_parent) {
				var html = el.innerHTML;
				html = html.replace(regex, '<span class="highlight">$&</span>');
				el.innerHTML = html;

				toggleSubmenu(el, true);
			} else {
				hide(el);
			}
		});
		hideParentLinks();
	};

	const resetHighlight = () => {
		const sidebarLinks = document.querySelectorAll('#sidebar .sidebar-link a');
		sidebarLinks.forEach((el, i) => {
			show(el);
			const title = el.dataset.title;
			el.innerHTML = title;

			toggleSubmenu(el, false);
		});
		showParentLinks();
	};
})();
