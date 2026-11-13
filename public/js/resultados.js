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

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("[data-category-carousel]").forEach((carousel) => {
    const track = carousel.querySelector("[data-category-track]");
    const cards = Array.from(track.querySelectorAll(".category-result"));
    const previous = carousel.querySelector("[data-category-prev]");
    const next = carousel.querySelector("[data-category-next]");
    let page = 0;
    // Cada página mantiene dos categorías, también en pantallas estrechas.
    const pageSize = () => 2;
    const update = () => {
      const size = pageSize();
      const totalPages = Math.ceil(cards.length / size);
      page = Math.min(page, Math.max(0, totalPages - 1));
      cards.forEach((card) => {
        const cardIndex = cards.indexOf(card);
        card.hidden = cardIndex < page * size || cardIndex >= (page + 1) * size;
      });
      track.style.transform = "none";
      previous.hidden = cards.length <= size || page === 0;
      next.hidden = cards.length <= size || page === totalPages - 1;
    };
    previous.addEventListener("click", () => { page -= 1; update(); });
    next.addEventListener("click", () => { page += 1; update(); });
    window.addEventListener("resize", update);
    update();
  });
});
