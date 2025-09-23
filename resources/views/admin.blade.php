<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio Vejarano</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css')}}">
</head>
<body>

    <!-- ==================== HEADER ==================== -->
   <header class="main-header">
    <div class="header-container">
        <!-- Único logo -->
        <div class="logo">
            <div class="bar red"></div>
            <img src="{{ asset('imagenes/logo.png') }}" alt="Laboratorio Vejarano">
        </div>
    </div>

 <!-- Administrador a la derecha -->
        <a href="/admin" class="admin-link">Operador</a>
    </div>
</header>




    <!-- LOGIN PANEL -->
  <div class="login-panel" id="login-panel">
    <h2>Iniciar sesión - Operador</h2>
    <input type="text" id="admin-usuario" placeholder="Usuario" />
    <input type="password" id="admin-password" placeholder="Contraseña" />
    <button id="login-btn">Ingresar</button>
    <div class="login-error" id="login-error">Usuario o contraseña incorrectos</div>
  </div>

  <!-- ADMIN PANEL -->
  <div class="admin-panel" id="admin-panel">
    <div class="header-panel">
      <div class="logo-box">Lorena Vejarano</div>
      <div>Panel de Administración - Control de Asistencias</div>
    </div>

    <div class="admin-filters">
      <select id="filter-ciudad">
        <option value="">Seleccionar ciudad</option>
        <option value="Popayán">Popayán</option>
        <option value="Yopal">Yopal</option>
        <option value="Cartagena">Cartagena</option>
        <option value="Medellín">Medellín</option>
        <option value="Cali">Cali</option>
        <option value="Bogotá">Bogotá</option>
      </select>

      <select id="filter-sede">
        <option value="">Seleccionar sede</option>
      </select>

      <input type="date" id="filter-fecha-inicio" title="Fecha inicio" />
      <input type="date" id="filter-fecha-fin" title="Fecha fin" />
      <input type="text" id="filter-buscar" placeholder="Buscar cedula (opcional)" />
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
      <tbody>
        <tr><td>Valentina Rodríguez Muñoz</td><td>Principal Popayán</td><td>2025-09-01</td><td>7:00 AM</td><td>5:00 PM</td></tr>
        <tr><td>María Gómez</td><td>Sede Norte Popayán</td><td>2025-09-01</td><td>8:00 AM</td><td>4:30 PM</td></tr>
        <tr><td>Juan Pérez</td><td>Central Yopal</td><td>2025-09-01</td><td>8:15 AM</td><td>6:10 PM</td></tr>
        <tr><td>Pedro López</td><td>Sur Yopal</td><td>2025-09-02</td><td>7:45 AM</td><td>5:15 PM</td></tr>
      </tbody>
    </table>

    <div class="no-data" id="no-data">No hay registros que coincidan con el filtro.</div>

    <button class="export-btn" id="export-btn">Exportar a Excel</button>
    <button class="logout-btn" id="logout-btn">Cerrar sesión</button>
  </div>

  <script>
    // --- LOGIN ---
    const loginPanel = document.getElementById("login-panel");
    const adminPanel = document.getElementById("admin-panel");
    const loginBtn = document.getElementById("login-btn");
    const loginError = document.getElementById("login-error");

    const adminUsuario = "admin";
    const adminPassword = "123456"; // Cambia esta contraseña cuando quieras

    loginBtn.addEventListener("click", () => {
      const usuario = document.getElementById("admin-usuario").value;
      const password = document.getElementById("admin-password").value;
      if (usuario === adminUsuario && password === adminPassword) {
        loginPanel.style.display = "none";
        adminPanel.style.display = "block";
        loginError.style.display = "none";
      } else {
        loginError.style.display = "block";
      }
    });

    // --- LOGOUT ---
    const logoutBtn = document.getElementById("logout-btn");
    logoutBtn.addEventListener("click", () => {
      window.location.href = "{{ route('home') }}"; // Redirige a home.blade.php
    });

    // --- SEDES POR CIUDAD ---
    const sedesPorCiudad = {
      "Popayán": ["Principal Popayán", "Sede Norte Popayán", "Sede Sur Popayán"],
      "Yopal": ["Central Yopal", "Sur Yopal"],
      "Cartagena": ["Centro Cartagena", "Norte Cartagena", "Sur Cartagena"],
      "Medellín": ["Centro Medellín", "Occidente Medellín", "Norte Medellín"],
      "Cali": ["Principal Cali", "Suroccidente Cali", "Oriente Cali"],
      "Bogotá": ["Norte Bogotá", "Sur Bogotá", "Centro Bogotá"]
    };

    const filtroCiudad = document.getElementById("filter-ciudad");
    const filtroSede = document.getElementById("filter-sede");
    const filtroFechaInicio = document.getElementById("filter-fecha-inicio");
    const filtroFechaFin = document.getElementById("filter-fecha-fin");
    const filtroBuscar = document.getElementById("filter-buscar");
    const tabla = document.getElementById("tabla-asistencias");
    const filas = tabla.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
    const noData = document.getElementById("no-data");

    function actualizarFiltroSede() {
      const ciudadSeleccionada = filtroCiudad.value;
      filtroSede.innerHTML = '<option value="">Seleccionar sede</option>';
      if (ciudadSeleccionada && sedesPorCiudad[ciudadSeleccionada]) {
        sedesPorCiudad[ciudadSeleccionada].forEach(sede => {
          const option = document.createElement("option");
          option.value = sede;
          option.textContent = sede;
          filtroSede.appendChild(option);
        });
      }
    }

    function filtrarTabla() {
      const sede = filtroSede.value.toLowerCase();
      const fechaInicio = filtroFechaInicio.value;
      const fechaFin = filtroFechaFin.value;
      const buscar = filtroBuscar.value.toLowerCase();
      let visibles = 0;
      for (let fila of filas) {
        const usuarioFila = fila.cells[0].textContent.toLowerCase();
        const sedeFila = fila.cells[1].textContent.toLowerCase();
        const fecha = fila.cells[2].textContent;
        const sedeCoincide = !sede || sedeFila === sede;
        const buscarCoincide = !buscar || usuarioFila.includes(buscar);
        let fechaCoincide = true;
        if (fechaInicio && fecha < fechaInicio) fechaCoincide = false;
        if (fechaFin && fecha > fechaFin) fechaCoincide = false;
        const mostrar = sedeCoincide }}

         // --- FILTRO DE TABLA ---
    function filtrarTabla() {
      const sede = filtroSede.value.toLowerCase();
      const fechaInicio = filtroFechaInicio.value;
      const fechaFin = filtroFechaFin.value;
      const buscar = filtroBuscar.value.toLowerCase();

      let visibles = 0;

      for (let fila of filas) {
        const usuarioFila = fila.cells[0].textContent.toLowerCase();
        const sedeFila = fila.cells[1].textContent.toLowerCase();
        const fecha = fila.cells[2].textContent;

        const sedeCoincide = !sede || sedeFila === sede;
        const buscarCoincide = !buscar || usuarioFila.includes(buscar);

        let fechaCoincide = true;
        if (fechaInicio && fecha < fechaInicio) fechaCoincide = false;
        if (fechaFin && fecha > fechaFin) fechaCoincide = false;

        const mostrar = sedeCoincide && buscarCoincide && fechaCoincide;
        fila.style.display = mostrar ? "" : "none";
        if (mostrar) visibles++;
      }

      noData.style.display = visibles === 0 ? "block" : "none";
    }

    filtroCiudad.addEventListener("change", () => {
      actualizarFiltroSede();
      filtrarTabla();
    });
    filtroSede.addEventListener("change", filtrarTabla);
    filtroFechaInicio.addEventListener("change", filtrarTabla);
    filtroFechaFin.addEventListener("change", filtrarTabla);
    filtroBuscar.addEventListener("keyup", filtrarTabla);

    // --- EXPORTAR TABLA A EXCEL ---
    document.getElementById("export-btn").addEventListener("click", () => {
      let tablaHTML = "<table border='1'><tr><th>USUARIO</th><th>SEDE</th><th>FECHA</th><th>HORA DE LLEGADA</th><th>HORA DE SALIDA</th></tr>";
      for (let fila of filas) {
        if (fila.style.display !== "none") {
          tablaHTML += "<tr>";
          for (let celda of fila.cells) {
            tablaHTML += `<td>${celda.textContent}</td>`;
          }
          tablaHTML += "</tr>";
        }
      }
      tablaHTML += "</table>";
      const blob = new Blob([tablaHTML], { type: "application/vnd.ms-excel" });
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.download = "asistencias.xls";
      link.click();
    });
  </script>

</body>
</html>