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
        <h2 class="text-center">Información del Paciente</h2>

        <!-- Mensajes de notificación -->
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success text-center">
                <?php echo htmlspecialchars($_GET['mensaje']); ?>
            </div>
        <?php endif; ?>

        <!-- Foto del Paciente -->
        <div class="text-center my-3">
            <img src="<?php echo htmlspecialchars($paciente['foto']); ?>" alt="Foto del paciente"
                 class="" width="150">
        </div>

        <!-- Tabla de datos -->
        <table class="table table-striped table-bordered">
            <tbody>
                <tr>
                    <th>Clave de Expediente</th>
                    <td><?php echo htmlspecialchars($paciente['clave_expediente']); ?></td>
                    <th>CURP</th>
                    <td><?php echo htmlspecialchars($paciente['curp']); ?></td>
                </tr>
                <tr>
                    <th>Edad</th>
                    <td><?php echo htmlspecialchars($paciente['edad']); ?></td>
                    <th>Sexo</th>
                    <td><?php echo htmlspecialchars($paciente['sexo']); ?></td>
                </tr>
                <tr>
                    <th>Fecha de Nacimiento</th>
                    <td><?php echo htmlspecialchars($paciente['fecha_nacimiento']); ?></td>
                    <th>Derechohabiencia</th>
                    <td><?php echo htmlspecialchars($paciente['derechohabiencia']); ?></td>
                </tr>
                <tr>
                    <th>Dirección</th>
                    <td colspan="3"><?php echo htmlspecialchars($paciente['direccion']); ?></td>
                </tr>
                <tr>
                    <th>Tipo de Sangre</th>
                    <td><?php echo htmlspecialchars($paciente['tipo_sangre']); ?></td>
                    <th>Religión</th>
                    <td><?php echo htmlspecialchars($paciente['religion']); ?></td>
                </tr>
                <tr>
                    <th>Ocupación</th>
                    <td><?php echo htmlspecialchars($paciente['ocupacion']); ?></td>
                    <th>Alergias</th>
                    <td><?php echo htmlspecialchars($paciente['alergias']); ?></td>
                </tr>
                <tr>
                    <th>Padecimientos Crónicos</th>
                    <td colspan="3"><?php echo htmlspecialchars($paciente['padecimientos']); ?></td>
                </tr>
                <tr>
                    <th>Fecha de Registro</th>
                    <td colspan="3"><?php echo htmlspecialchars($paciente['fecha_registro']); ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Botón de regreso -->
        <div class="text-center mt-4">
            <a href="panel.php" class="btn btn-outline-primary">
                <i class="fa-solid fa-arrow-left"></i> Volver al Panel
            </a>
        </div>
    </div>

    <!-- Incluir archivo JS -->
    <script src="js/scripts.js"></script>
</body>

</html>