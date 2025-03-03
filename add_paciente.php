<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="css/styles_desktop.css">
  <title>Panel de Usuario</title>
</head>

<body>
  <!-- Menú Lateral -->
  <div id="sidebar" class="sidebar">
    <div class="menu-header">
      <div class="menu-icon" id="menu-toggle">
        <i class="fa-solid fa-bars"></i>
      </div>
      <h2 style="color: white; text-align: center;">Menú</h2>
    </div>

    <!-- Opciones del menú -->
    <div class="submenu-container">
      <a href="javascript:void(0);" class="submenu-toggle"><i class="fa-solid fa-file-medical"></i> Expedientes Médicos</a>
      <div class="submenu">
        <a href="add_expediente.php">- Crear Expediente</a>
        <a href="search_expediente.php">- Buscar Expediente</a>
      </div>
    </div>

    <div class="submenu-container">
      <a href="javascript:void(0);" class="submenu-toggle"><i class="fa-solid fa-calendar-check"></i> Citas Médicas</a>
      <div class="submenu">
        <a href="#">- Agendar Cita</a>
        <a href="#">- Ver Cita</a>
      </div>
    </div>

    <div class="submenu-container">
      <a href="javascript:void(0);" class="submenu-toggle"><i class="fa-solid fa-file-import"></i> Digitalización y Actualización</a>
      <div class="submenu">
        <a href="#">- Digitalizar Historial</a>
        <a href="#">- Actualizar Historial</a>
      </div>
    </div>

    <div class="submenu-container">
      <a href="javascript:void(0);" class="submenu-toggle"><i class="fa-solid fa-gear"></i> Soporte</a>
      <div class="submenu">
        <a href="#">- Configuración</a>
        <a href="#">- Ayuda y Soporte Técnico</a>
      </div>
    </div>

    <!-- Cerrar sesión -->
    <a href="cerrar-sesion.php" class="close-sesion"><i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión</a>
  </div>

  <!-- Encabezado -->
  <header class="header">
    <h1>Panel de Usuario</h1>
    <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION["nombreUsuario"]); ?></strong>!</p>
  </header>

<!-- Contenido principal -->
<div class="container-panel">
    <h1>Agregar paciente.</h1>
    <p>Ingresa los datos para agregar el registro de un nuevo Paciente.</p>

    <!-- Formulario para agregar paciente -->
    <form action="agregar_paciente.php" method="POST">
        <label for="nombrePaciente">Nombre de Paciente:</label>
        <input type="text" id="nombrePaciente" name="nombrePaciente" required placeholder="Ingrese el nombre de paciente">

        <label for="primerApellido">Primer apellido:</label>
        <input type="text" id="primerApellido" name="nprimerApellido" required placeholder="Ingrese Primer Apellido">

        <label for="segundoApellido">Segundo Apellido:</label>
        <input type="text" id="segundoApellido" name="segundoApellido" required placeholder="Ingrese Segundo Apellido">

        <label for="fechaNacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fechaNacimiento" name="fechaNacimiento" required>

        <button type="submit" class="btn-submit">Agregar Paciente</button>
    </form>

    <!-- Botón para regresar al Panel -->
    <div class="back-to-panel">
        <a href="panel.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Volver al Panel</a>
    </div>
</div>

  <!-- Incluir archivo JS -->
  <script src="js/scripts.js"></script>
</body>

</html>
