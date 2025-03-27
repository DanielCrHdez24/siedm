<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

include 'conexion.php';

// Recibir el id_paciente de la URL
$id_paciente = $_GET['id_paciente'] ?? null;

if (!$id_paciente) {
    die("ID de paciente no proporcionado.");
}

// Consulta de datos del paciente
$sql = "SELECT * FROM pacientes WHERE id_paciente = ?";
if ($stmt = $link->prepare($sql)) {
    $stmt->bind_param("i", $id_paciente);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $paciente = $resultado->fetch_assoc();
    } else {
        die("Paciente no encontrado.");
    }
    $stmt->close();
} else {
    die("Error en la consulta: " . $link->error);
}

$link->close();
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
  <h1>Detalles del Paciente</h1>

<?php if (isset($_GET['mensaje'])): ?>
    <p style="color:green;"><?php echo htmlspecialchars($_GET['mensaje']); ?></p>
<?php endif; ?>

<div class="paciente-detalles">
    <img src="<?php echo htmlspecialchars($paciente['foto']); ?>" alt="Foto del paciente" width="150">
    <p><strong>Clave de Expediente:</strong> <?php echo htmlspecialchars($paciente['clave_expediente']); ?></p>
    <p><strong>CURP:</strong> <?php echo htmlspecialchars($paciente['curp']); ?></p>
    <p><strong>Edad:</strong> <?php echo htmlspecialchars($paciente['edad']); ?></p>
    <p><strong>Sexo:</strong> <?php echo htmlspecialchars($paciente['sexo']); ?></p>
    <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($paciente['fecha_nacimiento']); ?></p>
    <p><strong>Derechohabiencia:</strong> <?php echo htmlspecialchars($paciente['derechohabiencia']); ?></p>
    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($paciente['direccion']); ?></p>
    <p><strong>Tipo de Sangre:</strong> <?php echo htmlspecialchars($paciente['tipo_sangre']); ?></p>
    <p><strong>Religión:</strong> <?php echo htmlspecialchars($paciente['religion']); ?></p>
    <p><strong>Ocupación:</strong> <?php echo htmlspecialchars($paciente['ocupacion']); ?></p>
    <p><strong>Alergias:</strong> <?php echo htmlspecialchars($paciente['alergias']); ?></p>
    <p><strong>Padecimientos Crónicos:</strong> <?php echo htmlspecialchars($paciente['padecimientos']); ?></p>
    <p><strong>Fecha de Registro:</strong> <?php echo htmlspecialchars($paciente['fecha_registro']); ?></p>
</div>

<a href="panel.php" class="btn-back">Volver al panel</a>


  </div>

  <!-- Incluir archivo JS -->
  <script src="js/scripts.js"></script>
</body>

</html>
