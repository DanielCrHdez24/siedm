<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: index.php");
  exit();
}

// Incluir la conexión a la base de datos
include 'conexion.php';

// Obtener el id_usuario pasado en la URL
$id_usuario = $_GET['id_usuario'] ?? null; // Si no hay id_usuario en la URL, poner null

// Verificar si el id_usuario está presente
if ($id_usuario) {
    // Realizar la consulta para obtener el nombre del usuario según el id_usuario
    $sql = "SELECT nombre, primer_apellido, segundo_apellido FROM usuarios WHERE id_usuario = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_usuario);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nombre, $primer_apellido, $segundo_apellido);
        
        // Si encontramos el usuario
        if (mysqli_stmt_fetch($stmt)) {
            $nombre_completo = $nombre . " " . $primer_apellido . " " . $segundo_apellido;
        } else {
            echo "Usuario no encontrado.";
            exit();
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error en la consulta: " . mysqli_error($link);
        exit();
    }
} else {
    echo "ID de usuario no especificado.";
    exit();
}

mysqli_close($link);
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
    <p>Bienvenido, <strong><?php echo htmlspecialchars($nombre_completo); ?></strong>!</p>
  </header>

  <!-- Contenido principal -->
  <div class="container-panel">
    <h1>Agregar Expediente.</h1>
    <p>Ingresa los datos de <strong><?php echo htmlspecialchars($nombre . ' ' . $primer_apellido . ' ' . $segundo_apellido); ?></strong> para completar el nuevo expediente.</p>


    <!-- Formulario para agregar expediente -->
    <form action="insertar_expediente.php" method="POST" enctype="multipart/form-data">

      <label for="foto">Foto del Paciente:</label>
      <input type="file" id="foto" name="foto" accept="image/*" required>

      <label for="clave_expediente">Clave de Expediente:</label>
      <input type="text" id="clave_expediente" name="clave_expediente" required placeholder="Ingrese clave de expediente">

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

      <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
      <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

      <label for="derechohabiencia">Derechohabiencia:</label>
      <select id="derechohabiencia" name="derechohabiencia" required>
        <option value="">Seleccione una opción</option>
        <option value="IMSS">IMSS</option>
        <option value="ISSSTE">ISSSTE</option>
        <option value="INSABI">INSABI</option>
        <option value="Privado">Privado</option>
        <option value="Otro">Otro</option>
      </select>

      <label for="direccion">Domicilio:</label>
      <input type="text" id="direccion" name="direccion" required placeholder="Ingrese la dirección completa">

      <label for="tipo_sangre">Tipo de Sangre:</label>
      <select id="tipo_sangre" name="tipo_sangre" required>
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

      <!-- Campo oculto para pasar el id_usuario -->
      <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">

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
