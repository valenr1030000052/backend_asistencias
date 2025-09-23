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

    <!-- ==================== MAIN ==================== -->
    <section class="welcome-text" id="bienvenida">
    <p>
        Bienvenid@s a la empresa <strong>Lorena Vejarano</strong>.<br>
        Para registrar su hora de entrada o salida, por favor elija la sede en la que se encuentra y escanee el código de barras de su
        respectivo carnet. De esta manera el sistema identificará correctamente la sede y guardará su registro de asistencia en tiempo real.
    </p>
</section>

    <!-- ==================== SECCIÓN SEDES POR CIUDAD ==================== -->
<section class="sedes-mapa" id="sedes">
    <div class="sedes-container">
        <h2 class="sedes-titulo">Nuestras Sedes</h2>
        <p class="sedes-subtitulo">Selecciona tu sede desde la ciudad correspondiente</p>

        <div class="sede-grid">
            <!-- BOGOTÁ -->
            <div class="sede-card">
                <img src="{{ asset('imagenes/bogota.jpg') }}" alt="Bogotá">
                <h3>Bogotá</h3>
                <select id="sede-bogota">
                    <option value="">Selecciona una sede...</option>
                    <option value="principal">Sede Principal</option>
                    <option value="central-point">Central Point</option>
                    <option value="la84">LA 84</option>
                </select>
                <button onclick="irASede('bogota')">Ir a registro</button>
            </div>

            <!-- BARRANQUILLA -->
            <div class="sede-card">
                <img src="{{ asset('imagenes/barranquilla.jpg') }}" alt="Barranquilla">
                <h3>Barranquilla</h3>
                <select id="sede-barranquilla">
                    <option value="">Selecciona una sede...</option>
                    <option value="barranquilla">Sede Principal</option>
                </select>
                <button onclick="irASede('barranquilla')">Ir a registro</button>
            </div>

            <!-- CALI -->
            <div class="sede-card">
                <img src="{{ asset('imagenes/cali.jpg') }}" alt="Cali">
                <h3>Cali</h3>
                <select id="sede-cali">
                    <option value="">Selecciona una sede...</option>
                    <option value="cali-norte">Norte</option>
                    <option value="cali-sur">Sur</option>
                </select>
                <button onclick="irASede('cali')">Ir a registro</button>
            </div>

            <!-- Monteria-->
            <div class="sede-card">
                <img src="{{ asset('imagenes/monteria.jpg') }}" alt="Monteria">
                <h3>Monteria</h3>
                <select id="sede-monteria">
                    <option value="">Selecciona una sede...</option>
                    <option value="monteria">Sede Principal</option>
                </select>
                <button onclick="irASede('monteria')">Ir a registro</button>
            </div>

            <!-- YOPAL -->
            <div class="sede-card">
                <img src="{{ asset('imagenes/yopal.jpg') }}" alt="yopal">
                <h3>Yopal</h3>
                <select id="sede-yopal">
                    <option value="">Selecciona una sede...</option>
                    <option value="yopal">Sede Principal</option>
                </select>
                <button onclick="irASede('yopal')">Ir a registro</button>
            </div>

            <!-- CARTAGENA -->
            <div class="sede-card">
                <img src="{{ asset('imagenes/cartagena.jpg') }}" alt="Cartagena">
                <h3>Cartagena</h3>
                <select id="sede-cartagena">
                    <option value="">Selecciona una sede...</option>
                    <option value="cartagena">Sede Principal</option>
                </select>
                <button onclick="irASede('cartagena')">Ir a registro</button>
            </div>

            <!-- PUERTO TEJADA -->
            <div class="sede-card">
                <img src="{{ asset('imagenes/puertotejada.jpg') }}" alt="puerto tejada">
                <h3>Puerto Tejada</h3>
                <select id="sede-puertotejada">
                    <option value="">Selecciona una sede...</option>
                    <option value="puerto tejada">Sede Principal</option>
                </select>
                <button onclick="irASede('puertotejada')">Ir a registro</button>
            </div>

                   <!-- Santander de Quilichao -->
             <div class="sede-card">
            <img src="{{ asset('imagenes/santander.jpg') }}" alt="santander">
              <h3>Santander de Quilichao</h3>
             <select id="sede-santander"> <!-- ✅ corregido -->
           <option value="">Selecciona una sede...</option>
            <option value="santander">Sede Principal</option>
         </select>
          <button onclick="irASede('santander')">Ir a registro</button>
            </div>

            <!-- POPAYAN -->
            <div class="sede-card">
                <img src="{{ asset('imagenes/popayan.jpg') }}" alt="popayan">
                <h3>Popayan</h3>
                <select id="sede-popayan">
                    <option value="">Selecciona una sede...</option>
                    <option value="popayan">Sede Principal</option>
                </select>
                <button onclick="irASede('popayan')">Ir a registro</button>
            </div>
        </div>
    </div>
</section>

<script>
   function irASede(ciudad) {
  const select = document.getElementById(`sede-${ciudad}`);
  const sede = select.value;
  if (!sede) { alert("Por favor selecciona una sede."); return; }
  // redirige al backend (ahora en el mismo proyecto)
  window.location.href = `/escanear/${ciudad}/${sede}`;
}

    
</script>

 </body>
</html>
