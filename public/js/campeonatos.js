document.addEventListener("DOMContentLoaded", () => {
	const cards = Array.from(document.querySelectorAll(".championship-card"));
	const search = document.querySelector("[data-championship-search]");
	const status = document.querySelector("[data-championship-status]");
	const year = document.querySelector("[data-championship-year]");
	const previous = document.querySelector("[data-championship-prev]");
	const next = document.querySelector("[data-championship-next]");
	const pageLabel = document.querySelector("[data-championship-page]");
	const pagination = document.querySelector("[data-championship-pagination]");
	const empty = document.querySelector("[data-championship-empty]");
	let page = 0;

	[...new Set(cards.map((card) => card.dataset.year))].sort().reverse().forEach((value) => {
		const option = document.createElement("option");
		option.value = value;
		option.textContent = value;
		year.appendChild(option);
	});

	const update = () => {
		const query = search.value.trim().toLocaleLowerCase();
		const matches = cards.filter((card) => {
			const statusMatches = status.value === "all" || card.dataset.status === status.value;
			const yearMatches = year.value === "all" || card.dataset.year === year.value;
			const textMatches = !query || card.dataset.search.toLocaleLowerCase().includes(query);
			return statusMatches && yearMatches && textMatches;
		});
		const pageSize = 2;
		const totalPages = Math.ceil(matches.length / pageSize);
		page = Math.min(page, Math.max(0, totalPages - 1));
		cards.forEach((card) => { card.hidden = !matches.slice(page * pageSize, (page + 1) * pageSize).includes(card); });
		empty.hidden = matches.length > 0;
		pagination.hidden = totalPages <= 1;
		previous.disabled = page === 0;
		next.disabled = page >= totalPages - 1;
		pageLabel.textContent = totalPages > 0 ? `Página ${page + 1} de ${totalPages}` : "";
	};

	[search, status, year].forEach((control) => control.addEventListener("input", () => { page = 0; update(); }));
	previous.addEventListener("click", () => { page -= 1; update(); });
	next.addEventListener("click", () => { page += 1; update(); });
	update();
});
