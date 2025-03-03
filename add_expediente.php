<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: index.php");
  exit();
}

// Incluir la librería TCPDF
require_once('tcpdf_include.php');

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Recibir datos del formulario
  $claveExpediente = $_POST['claveExpediente'];
  $nombrePaciente = $_POST['nombrePaciente'];
  $primerApellido = $_POST['primerApellido'];
  $segundoApellido = $_POST['segundoApellido'];
  $curp = $_POST['curp'];
  $edad = $_POST['edad'];
  $sexo = $_POST['sexo'];
  $fechaNacimiento = $_POST['fechaNacimiento'];
  $derechohabiencia = $_POST['derechohabiencia'];
  $telefono = $_POST['telefono'];
  $direccion = $_POST['direccion'];
  $tipoSangre = $_POST['tipoSangre'];
  $religion = $_POST['religion'];
  $ocupacion = $_POST['ocupacion'];
  $alergias = $_POST['alergias'];
  $padecimientos = $_POST['padecimientos'];

  // Crear una instancia de TCPDF
  $pdf = new TCPDF();

  // Establecer información del documento
  $pdf->SetCreator('Sistema de Expedientes Médicos');
  $pdf->SetAuthor('Sistema de Expedientes Médicos');
  $pdf->SetTitle('Expediente Médico');
  $pdf->SetSubject('Expediente de ' . $nombrePaciente);

  // Agregar una página
  $pdf->AddPage();

  // Setear fuente
  $pdf->SetFont('helvetica', '', 12);

  // Contenido del expediente en formato HTML
  $html = '
    <h1>Expediente Médico</h1>
    <p><strong>Clave de Expediente:</strong> ' . $claveExpediente . '</p>
    <p><strong>Nombre del Paciente:</strong> ' . $nombrePaciente . ' ' . $primerApellido . ' ' . $segundoApellido . '</p>
    <p><strong>CURP:</strong> ' . $curp . '</p>
    <p><strong>Edad:</strong> ' . $edad . ' años</p>
    <p><strong>Sexo:</strong> ' . $sexo . '</p>
    <p><strong>Fecha de Nacimiento:</strong> ' . $fechaNacimiento . '</p>
    <p><strong>Derechohabiencia:</strong> ' . $derechohabiencia . '</p>
    <p><strong>Teléfono:</strong> ' . $telefono . '</p>
    <p><strong>Dirección:</strong> ' . $direccion . '</p>
    <p><strong>Tipo de Sangre:</strong> ' . $tipoSangre . '</p>
    <p><strong>Religión:</strong> ' . $religion . '</p>
    <p><strong>Ocupación:</strong> ' . $ocupacion . '</p>
    <p><strong>Alergias:</strong> ' . $alergias . '</p>
    <p><strong>Padecimientos Crónicos:</strong> ' . $padecimientos . '</p>
    ';

  // Escribir el contenido en el PDF
  $pdf->writeHTML($html, true, false, true, false, '');

  // Salvar el PDF en el servidor o enviarlo al navegador
  // Para descarga directa del archivo PDF
  $pdf->Output('expediente_' . $claveExpediente . '.pdf', 'D'); // 'D' para descarga
  exit; // Detener la ejecución después de generar el PDF
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
    <h1>Agregar Expediente.</h1>
    <p>Ingresa los datos para agregar el registro de un nuevo Expediente.</p>

    <!-- Formulario para agregar expediente -->
    <form action="agregar_expediente.php" method="POST" enctype="multipart/form-data">
      <label for="fotoPaciente">Foto del Paciente:</label>
      <input type="file" id="fotoPaciente" name="fotoPaciente" accept="image/*" required>

      <label for="claveExpediente">Clave de Expediente:</label>
      <input type="text" id="claveExpediente" name="claveExpediente" required placeholder="Ingrese clave de expediente">

      <label for="nombrePaciente">Nombre de Paciente:</label>
      <input type="text" id="nombrePaciente" name="nombrePaciente" required placeholder="Ingrese el nombre del paciente">

      <label for="primerApellido">Primer Apellido:</label>
      <input type="text" id="primerApellido" name="primerApellido" required placeholder="Ingrese primer apellido">

      <label for="segundoApellido">Segundo Apellido:</label>
      <input type="text" id="segundoApellido" name="segundoApellido" required placeholder="Ingrese segundo apellido">

      <label for="curp">CURP:</label>
      <input type="text" id="curp" name="curp" required placeholder="Ingrese CURP">

      <label for="edad">Edad:</label>
      <input type="number" id="edad" name="edad" required min="0" max="120" placeholder="Ingrese la edad">

      <label for="sexo">Sexo:</label>
      <select id="sexo" name="sexo" required>
        <option value="">Seleccione una opción</option>
        <option value="Masculino">Masculino</option>
        <option value="Femenino">Femenino</option>
      </select>

      <label for="fechaNacimiento">Fecha de Nacimiento:</label>
      <input type="date" id="fechaNacimiento" name="fechaNacimiento" required>

      <label for="derechohabiencia">Derechohabiencia:</label>
      <select id="derechohabiencia" name="derechohabiencia" required>
        <option value="">Seleccione una opción</option>
        <option value="IMSS">IMSS</option>
        <option value="ISSSTE">ISSSTE</option>
        <option value="INSABI">INSABI</option>
        <option value="Privado">Privado</option>
        <option value="Otro">Otro</option>
      </select>

      <label for="telefono">Teléfono de Contacto:</label>
      <input type="tel" id="telefono" name="telefono" required placeholder="Ingrese el teléfono de contacto">

      <label for="direccion">Domicilio:</label>
      <input type="text" id="direccion" name="direccion" required placeholder="Ingrese la dirección completa">

      <label for="tipoSangre">Tipo de Sangre:</label>
      <select id="tipoSangre" name="tipoSangre" required>
        <option value="">Seleccione una opción</option>
        <option value="A+">A+</option>
        <option value="A-">A-</option>
        <option value="B+">B+</option>
        <option value="B-">B-</option>
        <option value="AB+">AB+</option>
        <option value="AB-">AB-</option>
        <option value="O+">O+</option>
        <option value="O-">O-</option>
      </select>

      <label for="religion">Religión:</label>
      <input type="text" id="religion" name="religion" placeholder="Ingrese religión (opcional)">

      <label for="ocupacion">Ocupación:</label>
      <input type="text" id="ocupacion" name="ocupacion" required placeholder="Ingrese ocupación">

      <label for="alergias">Alergias:</label>
      <input type="text" id="alergias" name="alergias" placeholder="Ingrese alergias (si aplica)">

      <label for="padecimientos">Padecimientos Crónicos:</label>
      <input type="text" id="padecimientos" name="padecimientos" placeholder="Ingrese padecimientos crónicos (si aplica)">

      <button type="submit" class="btnaddExpediente">Agregar Expediente</button>
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