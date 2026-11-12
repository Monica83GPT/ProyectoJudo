document.addEventListener("DOMContentLoaded", () => {
	const input = document.querySelector("#foto");
	const preview = document.querySelector("#photo-preview");
	const fileName = document.querySelector("#photo-name");

	if (!input || !preview || !fileName) return;

	input.addEventListener("change", () => {
		const file = input.files?.[0];
		if (!file) {
			preview.src = "imagenes/SINFOTO.svg";
			fileName.textContent = "Se utilizará SINFOTO si no eliges una imagen.";
			return;
		}

		fileName.textContent = file.name;
		const reader = new FileReader();
		reader.addEventListener("load", () => {
			preview.src = String(reader.result);
		});
		reader.readAsDataURL(file);
	});
});
