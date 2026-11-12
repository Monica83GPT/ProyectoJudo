document.addEventListener("DOMContentLoaded", () => {
	const championships = Array.from(document.querySelectorAll(".championship-result[data-championship-id]"));
	const dropdowns = Array.from(document.querySelectorAll(".championship-dropdown"));

	const closeDropdown = (dropdown) => {
		const trigger = dropdown.querySelector(".championship-trigger");
		const menu = dropdown.querySelector(".championship-menu");
		trigger.setAttribute("aria-expanded", "false");
		menu.hidden = true;
	};

	const closeAllDropdowns = () => dropdowns.forEach(closeDropdown);

	const showChampionship = (championshipId) => {
		if (!championships.some((championship) => championship.dataset.championshipId === championshipId)) {
			return;
		}

		championships.forEach((championship) => {
			championship.hidden = championship.dataset.championshipId !== championshipId;
		});
	};

	dropdowns.forEach((dropdown) => {
		const trigger = dropdown.querySelector(".championship-trigger");
		const menu = dropdown.querySelector(".championship-menu");
		const options = Array.from(dropdown.querySelectorAll(".championship-option"));

		trigger.addEventListener("click", () => {
			const shouldOpen = trigger.getAttribute("aria-expanded") !== "true";
			closeAllDropdowns();
			if (shouldOpen) {
				trigger.setAttribute("aria-expanded", "true");
				menu.hidden = false;
			}
		});

		options.forEach((option) => {
			option.addEventListener("click", () => {
				showChampionship(option.dataset.championshipId);
				closeAllDropdowns();
			});
		});
	});

	document.addEventListener("click", (event) => {
		if (!event.target.closest(".championship-dropdown")) {
			closeAllDropdowns();
		}
	});

	document.addEventListener("keydown", (event) => {
		if (event.key === "Escape") {
			closeAllDropdowns();
		}
	});
});
