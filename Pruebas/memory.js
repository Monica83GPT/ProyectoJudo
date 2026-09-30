(function () {
const tablero = document.querySelector("#tablero");
const formulario = document.querySelector("#configuracion");
const selectorCasillas = document.querySelector("#casillas");
const puntosJugador = document.querySelector("#puntosJugador");
const puntosMaquina = document.querySelector("#puntosMaquina");
const turnoTexto = document.querySelector("#turnoTexto");
const mensaje = document.querySelector("#mensaje");
const tarjetaJugador = document.querySelector("#tarjetaJugador");
const tarjetaMaquina = document.querySelector("#tarjetaMaquina");
const secretos = new WeakMap();

const vehiculos = [
  ["coche", "#ffd6e0", cocheSvg],
  ["autobus", "#fff1a8", autobusSvg],
  ["avion", "#d9f4ff", avionSvg],
  ["barco", "#c9f2e5", barcoSvg],
  ["tren", "#e6d7ff", trenSvg],
  ["camion", "#ffd8b5", camionSvg],
  ["cohete", "#f8d7ff", coheteSvg],
  ["tractor", "#d7f7c2", tractorSvg],
  ["ambulancia", "#d8ecff", ambulanciaSvg],
  ["helicoptero", "#ffe5b8", helicopteroSvg],
  ["moto", "#ffcfd2", motoSvg],
  ["submarino", "#c7efff", submarinoSvg]
];

let cartas = [];
let seleccionadas = [];
let bloqueado = false;
let turno = "jugador";
let marcadorJugador = 0;
let marcadorMaquina = 0;

formulario.addEventListener("submit", (evento) => {
  evento.preventDefault();
  iniciarPartida(Number(selectorCasillas.value));
});

function iniciarPartida(totalCasillas) {
  const parejas = totalCasillas / 2;
  const elegidos = vehiculos.slice(0, parejas);
  const datosCartas = mezclar([...elegidos, ...elegidos].map((vehiculo, indice) => ({
    pareja: indice % parejas,
    nombre: vehiculo[0],
    color: vehiculo[1],
    svg: vehiculo[2]
  })));

  cartas = datosCartas.map((datos, indice) => {
    const carta = {
      id: indice,
      encontrada: false
    };

    secretos.set(carta, datos);
    return carta;
  });

  seleccionadas = [];
  bloqueado = false;
  turno = "jugador";
  marcadorJugador = 0;
  marcadorMaquina = 0;
  tablero.style.setProperty("--columnas", calcularColumnas(totalCasillas));
  tablero.style.setProperty("--columnas-movil", totalCasillas <= 12 ? 3 : 4);
  tablero.style.setProperty("--tamano-carta", calcularTamanoCarta(totalCasillas));
  pintarTablero();
  actualizarPanel("Elige dos cartas para encontrar una pareja.");
}

function pintarTablero() {
  tablero.innerHTML = "";

  cartas.forEach((carta) => {
    const boton = document.createElement("button");
    boton.className = "carta";
    boton.type = "button";
    boton.dataset.id = carta.id;
    boton.setAttribute("aria-label", "Carta oculta");
    boton.style.setProperty("--color", secretos.get(carta).color);
    boton.innerHTML = `
      <span class="interior">
        <span class="cara trasera"></span>
        <span class="cara frontal"></span>
      </span>
    `;
    boton.addEventListener("click", () => elegirCarta(carta.id));
    tablero.appendChild(boton);
  });
}

function elegirCarta(id) {
  if (bloqueado || turno !== "jugador") return;

  const carta = cartas.find((item) => item.id === id);
  if (!carta || carta.encontrada || seleccionadas.includes(carta)) return;

  mostrarCarta(carta);
  seleccionadas.push(carta);

  if (seleccionadas.length === 2) {
    resolverTurno();
  }
}

function resolverTurno() {
  bloqueado = true;
  const [primera, segunda] = seleccionadas;
  const acierto = secretos.get(primera).pareja === secretos.get(segunda).pareja;

  setTimeout(() => {
    if (acierto) {
      primera.encontrada = true;
      segunda.encontrada = true;
      marcadorJugador++;
      marcarEncontrada(primera);
      marcarEncontrada(segunda);
      seleccionadas = [];
      bloqueado = false;
      actualizarPanel("Has encontrado una pareja. Sigues jugando.");
      comprobarFinal();
      return;
    }

    ocultarCarta(primera);
    ocultarCarta(segunda);
    seleccionadas = [];
    turno = "maquina";
    actualizarPanel("No era pareja. Ahora juega la maquina.");
    setTimeout(turnoMaquina, 700);
  }, 850);
}

function turnoMaquina() {
  if (comprobarFinal()) return;

  const disponibles = cartas.filter((carta) => !carta.encontrada);
  const [primera, segunda] = mezclar([...disponibles]).slice(0, 2);
  mostrarCarta(primera);

  setTimeout(() => {
    mostrarCarta(segunda);

    setTimeout(() => {
      if (secretos.get(primera).pareja === secretos.get(segunda).pareja) {
        primera.encontrada = true;
        segunda.encontrada = true;
        marcadorMaquina++;
        marcarEncontrada(primera);
        marcarEncontrada(segunda);
        actualizarPanel("La maquina encontro una pareja. Repite turno.");
        if (!comprobarFinal()) {
          setTimeout(turnoMaquina, 850);
        }
        return;
      }

      ocultarCarta(primera);
      ocultarCarta(segunda);
      turno = "jugador";
      bloqueado = false;
      actualizarPanel("La maquina fallo. Te toca.");
    }, 900);
  }, 650);
}

function mostrarCarta(carta) {
  const elemento = obtenerElemento(carta);
  const frontal = elemento.querySelector(".frontal");
  const datos = secretos.get(carta);

  if (!frontal.querySelector("img")) {
    const imagen = document.createElement("img");
    imagen.src = imagenSvg(datos.svg());
    imagen.alt = "Vehiculo infantil";
    frontal.appendChild(imagen);
  }

  elemento.classList.add("volteada");
  elemento.setAttribute("aria-label", "Carta descubierta");
}

function ocultarCarta(carta) {
  const elemento = obtenerElemento(carta);
  const frontal = elemento.querySelector(".frontal");

  elemento.classList.remove("volteada");
  elemento.setAttribute("aria-label", "Carta oculta");

  setTimeout(() => {
    if (!carta.encontrada) {
      frontal.textContent = "";
    }
  }, 430);
}

function marcarEncontrada(carta) {
  const elemento = obtenerElemento(carta);
  elemento.classList.add("encontrada");
  elemento.disabled = true;
}

function obtenerElemento(carta) {
  return tablero.querySelector(`[data-id="${carta.id}"]`);
}

function comprobarFinal() {
  const terminado = cartas.every((carta) => carta.encontrada);
  if (!terminado) return false;

  bloqueado = true;
  let texto = "Empate. Buena partida.";
  if (marcadorJugador > marcadorMaquina) texto = "Ganaste la partida.";
  if (marcadorMaquina > marcadorJugador) texto = "Gano la maquina.";
  actualizarPanel(texto);
  return true;
}

function actualizarPanel(texto) {
  puntosJugador.textContent = marcadorJugador;
  puntosMaquina.textContent = marcadorMaquina;
  turnoTexto.textContent = turno === "jugador" ? "Jugador" : "Maquina";
  mensaje.textContent = texto;
  tarjetaJugador.classList.toggle("activo", turno === "jugador");
  tarjetaMaquina.classList.toggle("activo", turno === "maquina");
}

function calcularColumnas(totalCasillas) {
  if (totalCasillas === 8) return 4;
  if (totalCasillas === 12) return 4;
  if (totalCasillas === 16) return 4;
  if (totalCasillas === 20) return 5;
  return 6;
}

function calcularTamanoCarta(totalCasillas) {
  if (totalCasillas <= 12) return "130px";
  if (totalCasillas <= 16) return "112px";
  if (totalCasillas <= 20) return "100px";
  return "88px";
}

function mezclar(lista) {
  return lista
    .map((valor) => ({ valor, orden: Math.random() }))
    .sort((a, b) => a.orden - b.orden)
    .map((item) => item.valor);
}

function imagenSvg(svg) {
  return `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`;
}

function baseSvg(contenido) {
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">${contenido}</svg>`;
}

function rueda(x, y) {
  return `<circle cx="${x}" cy="${y}" r="10" fill="#526a92"/><circle cx="${x}" cy="${y}" r="4" fill="#ffffff"/>`;
}

function cocheSvg() {
  return baseSvg('<rect x="22" y="54" width="76" height="28" rx="12" fill="#f7a9a8"/><path d="M38 54l10-18h28l12 18z" fill="#8ecae6"/><circle cx="51" cy="46" r="4" fill="#fff"/><circle cx="72" cy="46" r="4" fill="#fff"/>' + rueda(42, 84) + rueda(82, 84));
}

function autobusSvg() {
  return baseSvg('<rect x="18" y="35" width="84" height="48" rx="10" fill="#ffd166"/><rect x="28" y="45" width="16" height="14" rx="3" fill="#bde0fe"/><rect x="50" y="45" width="16" height="14" rx="3" fill="#bde0fe"/><rect x="72" y="45" width="16" height="14" rx="3" fill="#bde0fe"/><rect x="76" y="64" width="14" height="19" rx="2" fill="#fdfcdc"/>' + rueda(38, 85) + rueda(82, 85));
}

function avionSvg() {
  return baseSvg('<path d="M18 65l84-32-24 34 18 22-30-10-20 22-2-31z" fill="#90dbf4"/><path d="M44 70l34-37-20 42z" fill="#ffffff" opacity=".75"/><circle cx="62" cy="58" r="4" fill="#526a92"/>');
}

function barcoSvg() {
  return baseSvg('<path d="M24 66h72L84 91H36z" fill="#80ed99"/><path d="M57 26v36H31z" fill="#ffcad4"/><path d="M62 34v28h31z" fill="#bdb2ff"/><rect x="56" y="24" width="5" height="43" rx="2" fill="#526a92"/><path d="M22 94c8 5 16 5 24 0 8 5 16 5 24 0 8 5 16 5 28 0" fill="none" stroke="#73c2fb" stroke-width="6" stroke-linecap="round"/>');
}

function trenSvg() {
  return baseSvg('<rect x="18" y="42" width="68" height="35" rx="8" fill="#a0c4ff"/><rect x="74" y="52" width="20" height="25" rx="5" fill="#ffc6ff"/><rect x="29" y="50" width="14" height="12" rx="2" fill="#fff"/><rect x="50" y="50" width="14" height="12" rx="2" fill="#fff"/><path d="M18 82h80" stroke="#526a92" stroke-width="5" stroke-linecap="round"/>' + rueda(34, 82) + rueda(62, 82) + rueda(88, 82));
}

function camionSvg() {
  return baseSvg('<rect x="17" y="48" width="52" height="32" rx="7" fill="#ffb703"/><path d="M69 58h20l14 22H69z" fill="#fb8500"/><rect x="78" y="62" width="12" height="10" rx="2" fill="#caf0f8"/>' + rueda(37, 83) + rueda(83, 83));
}

function coheteSvg() {
  return baseSvg('<path d="M60 16c18 16 20 43 8 65H52c-12-22-10-49 8-65z" fill="#ffafcc"/><circle cx="60" cy="43" r="10" fill="#bde0fe"/><path d="M52 77l-15 18 4-28z" fill="#a2d2ff"/><path d="M68 77l15 18-4-28z" fill="#a2d2ff"/><path d="M52 84h16l-8 22z" fill="#ffd166"/>');
}

function tractorSvg() {
  return baseSvg('<rect x="38" y="46" width="34" height="26" rx="4" fill="#95d5b2"/><rect x="66" y="58" width="27" height="15" rx="3" fill="#74c69d"/><rect x="44" y="34" width="19" height="16" rx="3" fill="#bde0fe"/><circle cx="43" cy="78" r="17" fill="#526a92"/><circle cx="43" cy="78" r="8" fill="#fff"/><circle cx="86" cy="78" r="10" fill="#526a92"/><circle cx="86" cy="78" r="4" fill="#fff"/>');
}

function ambulanciaSvg() {
  return baseSvg('<rect x="18" y="48" width="78" height="32" rx="8" fill="#ffffff"/><path d="M67 55h21l10 25H67z" fill="#d8ecff"/><path d="M38 55v18M29 64h18" stroke="#ff758f" stroke-width="7" stroke-linecap="round"/><path d="M18 80h83" stroke="#ffcad4" stroke-width="5"/>' + rueda(38, 84) + rueda(82, 84));
}

function helicopteroSvg() {
  return baseSvg('<path d="M35 52h43c12 0 20 8 20 18H45c-10 0-18-8-18-18z" fill="#cdb4db"/><circle cx="80" cy="61" r="9" fill="#bde0fe"/><path d="M57 52V35M28 35h58M23 35h68" stroke="#526a92" stroke-width="5" stroke-linecap="round"/><path d="M98 63l14-10v20z" fill="#ffc8dd"/><path d="M45 78h38" stroke="#526a92" stroke-width="5" stroke-linecap="round"/>');
}

function motoSvg() {
  return baseSvg('<circle cx="36" cy="80" r="15" fill="#526a92"/><circle cx="84" cy="80" r="15" fill="#526a92"/><path d="M36 80l19-24h17l12 24M55 56l-8 24h37" fill="none" stroke="#ffafcc" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/><path d="M72 56h15" stroke="#526a92" stroke-width="5" stroke-linecap="round"/>');
}

function submarinoSvg() {
  return baseSvg('<ellipse cx="61" cy="68" rx="45" ry="20" fill="#8ecae6"/><path d="M94 68l18-14v28z" fill="#90dbf4"/><rect x="54" y="37" width="14" height="17" rx="3" fill="#8ecae6"/><path d="M61 37V25h20" stroke="#526a92" stroke-width="5" stroke-linecap="round"/><circle cx="45" cy="68" r="6" fill="#fff"/><circle cx="64" cy="68" r="6" fill="#fff"/><circle cx="83" cy="68" r="6" fill="#fff"/>');
}

iniciarPartida(Number(selectorCasillas.value));
})();
