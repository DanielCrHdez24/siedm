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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
        <a href="add_cita.php">- Agendar Cita</a>
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
    <h1>Bienvenido al Sistema Integral de Expedientes Digitales Médicos.</h1>
    <p>Selecciona una opción para comenzar.</p>

    <!-- Iconos de opciones -->
    <div class="row g-4">
      <!-- Icono para agregar paciente -->
      <div class="col-md-4 text-center">
        <div class="option-icon">
          <i class="fa-solid fa-user-injured fa-4x"></i>
        </div>
        <h5>Agregar Paciente</h5>
        <p>Da de alta a un nuevo paciente en el sistema.</p>
        <a href="add_paciente.php" class="btn-back">Ir</a>
      </div>

      <!-- Icono para agregar médico -->
      <div class="col-md-4 text-center">
        <div class="option-icon">
          <i class="fa-solid fa-user-doctor fa-4x"></i>
        </div>
        <h5>Agregar Médico</h5>
        <p>Registra un nuevo médico en el sistema.</p>
        <a href="add_medico.php" class="btn-back">Ir</a>
      </div>

      <!-- Icono para gestionar citas -->
      <div class="col-md-4 text-center">
        <div class="option-icon">
          <i class="fa-solid fa-calendar-check fa-4x"></i>
        </div>
        <h5>Gestionar Citas</h5>
        <p>Agendar y gestionar citas médicas.</p>
        <a href="add_cita.php" class="btn-back">Ir</a>
      </div>
    </div>
  </div>

  <!-- Incluir archivo JS -->
  <script src="js/scripts.js"></script>
</body>

</html>
