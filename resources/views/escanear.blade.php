<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio Vejarano - Escáner</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css')}}">

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    
</head>
<body>

<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <div class="bar red"></div>
            <img src="{{ asset('imagenes/logo.png') }}" alt="Laboratorio Vejarano">
        </div>
    </div>
    <a href="/admin" class="admin-link">Operador</a>
</header>

<div class="scanner-container">
    <h2 class="titulo">Registro en {{ ucfirst($ciudad) }} - {{ ucfirst($sede) }}</h2>
    <p class="fecha" id="fecha-hora">📅 Cargando...</p>

    <div class="loader" id="loader">⏳ Procesando...</div>
    <div class="success" id="success"></div>
    <div class="error" id="error"></div>

    <!-- Input manual -->
    <div class="manual-input">
        <input type="text" id="barcode-input" placeholder="Escanea o escribe el código" autofocus>
    </div>

    <!-- Tabla de registros -->
    <table id="registros-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Hora de Entrada</th>
                <th>Hora de Salida</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div class="salir-container">
        <button class="btn-salir" onclick="window.location.href='{{ route('home') }}'">Salir</button>
    </div>
</div>

<!-- ✅ Sonidos -->
<audio id="sound-entrada" src="{{ asset('sonidos/entrada.mp3') }}" preload="auto"></audio>
<audio id="sound-salida" src="{{ asset('sonidos/salida.mp3') }}" preload="auto"></audio>
<audio id="sound-error" src="{{ asset('sonidos/error.mp3') }}" preload="auto"></audio>

<script>
const loader = document.getElementById("loader");
const success = document.getElementById("success");
const errorBox = document.getElementById("error");
const input = document.getElementById("barcode-input");
const table = document.getElementById("registros-table");
const tableBody = table.querySelector("tbody");
const soundEntrada = document.getElementById("sound-entrada");
const soundSalida = document.getElementById("sound-salida");
const soundError = document.getElementById("sound-error");

let contador = 0;
let typingTimer;
const typingDelay = 120; // ms (detecta fin de lectura de escáner)

// ✅ Actualizar fecha y hora en tiempo real
function actualizarFechaHora() {
    const ahora = new Date();
    const opciones = { weekday: "long", year: "numeric", month: "long", day: "numeric",
                       hour: "2-digit", minute: "2-digit", second: "2-digit" };
    document.getElementById("fecha-hora").textContent =
        "📅 " + ahora.toLocaleDateString("es-CO", opciones);
}
setInterval(actualizarFechaHora, 1000);
actualizarFechaHora();

// ✅ Cargar registros
async function cargarRegistros() {
    try {
        const res = await axios.get("/api/registros");
        tableBody.innerHTML = "";
        contador = 0;
        res.data.forEach(r => agregarFila(r));
        table.style.display = res.data.length > 0 ? "table" : "none";
    } catch (err) {
        console.error(err);
    }
}
setInterval(cargarRegistros, 5000);
cargarRegistros();

// ✅ Agregar fila
function agregarFila(registro) {
    contador++;
    const row = document.createElement("tr");
    row.innerHTML = `
        <td>${contador}</td>
        <td>${registro.usuario?.codigo_barras ?? "-"}</td>
        <td>${registro.usuario?.nombre ?? "Desconocido"}</td>
        <td>${registro.hora_entrada ?? "-"}</td>
        <td>${registro.hora_salida ?? "-"}</td>
    `;
    tableBody.prepend(row);
}

// ✅ Registrar entrada/salida
async function registrar() {
    const codigo = input.value.trim();
    if (!codigo) return;

    loader.style.display = "block";
    success.style.display = "none";
    errorBox.style.display = "none";

    try {
        const res = await axios.post("/api/scan", {
            codigo_barras: codigo,
            sede_id: null
        });

        loader.style.display = "none";
        success.textContent = `✅ ${res.data.tipo.toUpperCase()} registrada para ${res.data.usuario} (${res.data.hora})`;
        success.style.display = "block";

        // ✅ Agregar fila de inmediato
        agregarFila({
            usuario: { codigo_barras: codigo, nombre: res.data.usuario },
            hora_entrada: res.data.tipo === "entrada" ? res.data.hora : "-",
            hora_salida: res.data.tipo === "salida" ? res.data.hora : "-"
        });

        input.value = "";

        if (res.data.tipo === "entrada") soundEntrada.play();
        else if (res.data.tipo === "salida") soundSalida.play();

    } catch (err) {
        loader.style.display = "none";
        errorBox.textContent = err.response?.data?.message ?? "Error en el registro.";
        errorBox.style.display = "block";
        soundError.play();
    }
}

// ✅ Detectar fin de escritura automática (lector de códigos)
input.addEventListener("input", () => {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => {
        registrar();
    }, typingDelay);
});

// ✅ Mantener foco siempre en el input
window.addEventListener("load", () => input.focus());
document.addEventListener("click", () => input.focus());
</script>

</body>
</html>