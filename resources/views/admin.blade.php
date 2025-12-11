<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Laboratorio Vejarano</title>

    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    <style>
        #tabla-asistencias td, #tabla-asistencias th {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }
    </style>
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

<!-- LOGIN -->
<div class="login-panel" id="login-panel">
    <h2>Iniciar sesión - Operador</h2>
    <input type="text" id="admin-usuario" placeholder="Usuario" />
    <input type="password" id="admin-password" placeholder="Contraseña" />
    <button id="login-btn">Ingresar</button>
    <div class="login-error" id="login-error">Usuario o contraseña incorrectos</div>
</div>

<!-- PANEL ADMIN -->
<div class="admin-panel" id="admin-panel" style="display:none;">
    <div class="header-panel">
        <div class="logo-box">Lorena Vejarano</div>
        <div>Panel de Administración - Control de Asistencias</div>
    </div>

    <div class="admin-filters">
        <input type="text" id="filter-buscar" placeholder="Buscar nombre o cédula" />

        <input type="date" id="filter-fecha-inicio" />
        <input type="date" id="filter-fecha-fin" />

        <select id="filter-ciudad">
            <option value="">Seleccionar ciudad</option>
        </select>

        <select id="filter-sede">
            <option value="">Seleccionar sede</option>
        </select>
    </div>

    <table id="tabla-asistencias">
        <thead>
            <tr>
                <th>USUARIO</th>
                <th>SEDE</th>
                <th>FECHA</th>
                <th>HORA DE LLEGADA</th>
                <th>HORA DE SALIDA</th>
            </tr>
        </thead>
        <tbody id="tbody-registros"></tbody>
    </table>

    <div class="no-data" id="no-data" style="display:none;">No hay registros que coincidan.</div>

    <button class="export-btn" id="export-btn">Exportar a Excel</button>
    <button class="logout-btn" id="logout-btn">Cerrar sesión</button>
</div>

<script>
/* ===================== LOGIN ===================== */
const loginPanel = document.getElementById("login-panel");
const adminPanel = document.getElementById("admin-panel");

document.getElementById("login-btn").addEventListener("click", () => {
    let u = document.getElementById("admin-usuario").value;
    let p = document.getElementById("admin-password").value;

    if (u === "admin" && p === "123456") {
        loginPanel.style.display = "none";
        adminPanel.style.display = "block";

        cargarCiudades();
        cargarSedes();
        cargarRegistros();
    } else {
        document.getElementById("login-error").style.display = "block";
    }
});

/* ===================== LOGOUT ===================== */
document.getElementById("logout-btn").addEventListener("click", () => {
    window.location.href = "{{ route('home') }}";
});

/* ===================== CARGAR CIUDADES ===================== */
function cargarCiudades() {
    fetch("/api/admin/ciudades")
        .then(r => r.json())
        .then(resp => {
            const ciudadSelect = document.getElementById("filter-ciudad");
            ciudadSelect.innerHTML = `<option value="">Seleccionar ciudad</option>`;

            resp.data.forEach(c => {
                let opt = document.createElement("option");
                opt.value = c.id;
                opt.textContent = c.nombre;
                ciudadSelect.appendChild(opt);
            });
        })
        .catch(err => console.error("Error cargando ciudades:", err));
}

/* ===================== CARGAR SEDES SEGÚN CIUDAD ===================== */
function cargarSedes() {
    const ciudad = document.getElementById("filter-ciudad").value;

    let url = "/api/admin/sedes";
    if (ciudad) url += `?ciudad_id=${ciudad}`;

    fetch(url)
        .then(r => r.json())
        .then(resp => {
            const sedesSelect = document.getElementById("filter-sede");
            sedesSelect.innerHTML = `<option value="">Seleccionar sede</option>`;

            resp.data.forEach(s => {
                let opt = document.createElement("option");
                opt.value = s.id;
                opt.textContent = s.nombre + " - " + s.ciudad.nombre;
                sedesSelect.appendChild(opt);
            });
        })
        .catch(err => console.error("Error cargando sedes:", err));
}

/* ===================== CARGAR REGISTROS ===================== */
function cargarRegistros() {
    let params = new URLSearchParams();

    let buscar = document.getElementById("filter-buscar").value;
    let inicio = document.getElementById("filter-fecha-inicio").value;
    let fin = document.getElementById("filter-fecha-fin").value;
    let ciudad = document.getElementById("filter-ciudad").value;
    let sede = document.getElementById("filter-sede").value;

    if (buscar) params.append("buscar", buscar);
    if (inicio) params.append("fecha_inicio", inicio);
    if (fin) params.append("fecha_fin", fin);
    if (ciudad) params.append("ciudad_id", ciudad);
    if (sede) params.append("sede_id", sede);

    fetch("/api/admin/registros?" + params.toString())
        .then(r => r.json())
        .then(resp => {
            let tbody = document.getElementById("tbody-registros");
            tbody.innerHTML = "";

            let registros = resp.data.data;
            let visibles = 0;

            registros.forEach(r => {
                visibles++;

                let tr = `
                    <tr>
                        <td>${r.usuario.nombre}</td>
                        <td>${r.sede.nombre}</td>
                        <td>${r.fecha ?? "--"}</td>
                        <td>${r.hora_entrada ?? "--"}</td>
                        <td>${r.hora_salida ?? "--"}</td>
                    </tr>
                `;
                tbody.innerHTML += tr;
            });

            document.getElementById("no-data").style.display =
                visibles === 0 ? "block" : "none";
        });
}

/* ===================== FILTROS ===================== */
document.getElementById("filter-buscar").addEventListener("keyup", cargarRegistros);
document.getElementById("filter-fecha-inicio").addEventListener("change", cargarRegistros);
document.getElementById("filter-fecha-fin").addEventListener("change", cargarRegistros);
document.getElementById("filter-ciudad").addEventListener("change", () => {
    cargarSedes();
    cargarRegistros();
});
document.getElementById("filter-sede").addEventListener("change", cargarRegistros);

/* ===================== EXPORTAR EXCEL ===================== */
document.getElementById("export-btn").addEventListener("click", () => {
    let tabla = document.getElementById("tabla-asistencias").outerHTML;
    let blob = new Blob([tabla], { type: "application/vnd.ms-excel" });
    let a = document.createElement("a");

    a.href = URL.createObjectURL(blob);
    a.download = "asistencias.xls";
    a.click();
});
</script>

</body>
</html>