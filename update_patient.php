<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
include 'conexion.php';

// Obtener el id_paciente de la URL
$id_paciente = $_GET['id_paciente'] ?? null; 
if ($id_paciente === null) {
    die("ID de paciente no proporcionado.");
}

// Consultar el id_usuario asociado al paciente
$sql = "SELECT id_usuario FROM pacientes WHERE id_paciente = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id_paciente);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_usuario);

    if (!mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        die("Paciente no encontrado.");
    }

    mysqli_stmt_close($stmt);
} else {
    error_log("Error en consulta SQL: " . mysqli_error($link));
    die("Error interno.");
}

// Ahora que tenemos el id_usuario, consultamos los datos del usuario
$sql_usuario = "SELECT nombre, primer_apellido, segundo_apellido,correo,telefono FROM usuarios WHERE id_usuario = ?";
if ($stmt_usuario = mysqli_prepare($link, $sql_usuario)) {
    mysqli_stmt_bind_param($stmt_usuario, 'i', $id_usuario);
    mysqli_stmt_execute($stmt_usuario);
    mysqli_stmt_bind_result($stmt_usuario, $nombre, $primer_apellido, $segundo_apellido,$correo,$telefono);

    if (!mysqli_stmt_fetch($stmt_usuario)) {
        mysqli_stmt_close($stmt_usuario);
        die("Usuario no encontrado.");
    }

    mysqli_stmt_close($stmt_usuario);
} else {
    error_log("Error en consulta SQL de usuario: " . mysqli_error($link));
    die("Error interno.");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles_desktop.css">
    <title>Modificar Paciente</title>
</head>

<body class="principal">
    <div class="wrapper"> <!-- Wrapper para agrupar todo -->
        <header class="header">
            <a href="#" class="logo">
                <img src="./images/logo.png" alt="Logo SIEDM" width="150px" />
            </a>
            <nav class="navbar">
                <a href="panel.php">Dashboard</a>
                <a href="perfil.php">Mi perfil</a>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <!-- Menú para Admin o Médico-->
                    <a href="users.php">Gestión de Usuarios</a>
                <?php endif; ?>

                <a href="citas.php">Gestión de Citas</a>
                <?php if ($idRol == 1 || $idRol == 2 || $idRol == 4): ?>
                    <!-- Menú para Admin, Médico o Paciente-->
                    <a href="historial_medico.php">Historial Médico</a>
                <?php endif; ?>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <!-- Menú para Admin o Médico-->
                    <a href="configuración.php">Configuración</a>
                <?php endif; ?>
                <a href="logout.php" class="logout-link">Cerrar sesión</a>
            </nav>
        </header>

        <div class="container">
            <h2>Modificar Paciente</h2>
            <p>Modifique los datos solicitados en los campos.</p>
            
            <form class="form" action="actualizar_paciente.php" method="POST">
                <!-- Campo oculto para enviar el id_paciente -->
                <input type="hidden" name="id_paciente" value="<?php echo ($id_paciente); ?>">

                <label for="nombre">Nombre de Paciente:</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ingrese el nombre de paciente" value="<?php echo htmlspecialchars($nombre); ?>">
                
                <label for="primer_apellido">Primer apellido:</label>
                <input type="text" id="primer_apellido" name="primer_apellido" required placeholder="Ingrese Primer Apellido" value="<?php echo htmlspecialchars($primer_apellido); ?>">
                
                <label for="segundo_apellido">Segundo Apellido:</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido" required placeholder="Ingrese Segundo Apellido" value="<?php echo htmlspecialchars($segundo_apellido); ?>">
                
                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" required placeholder="Ingrese correo electrónico" value="<?php echo htmlspecialchars($correo); ?>">
                
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" required placeholder="Ingrese teléfono de contacto" pattern="[0-9]{10}" maxlength="10" value="<?php echo htmlspecialchars($telefono); ?>">

                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required placeholder="Ingrese nueva contraseña">
                
                <label for="contrasena2">Confirma la contraseña:</label>
                <input type="password" id="contrasena2" name="contrasena2" required placeholder="Confirma la nueva contraseña">
                <p> </p>
                <input type="text" id="id_rol" name="id_rol" required hidden value="4">
                
                <button type="submit" class="button">Actualizar</button>
                <button type="button" class="button" onclick="window.location.href='users.php';">Cancelar</button>
                
            </form>
        </div>

        <footer class="footer">
            <p>Daniel Cruz Hernández - 22300104</p>
            <p>Nicolás Misael López Cruz - 22300149</p>
            <p>Karen Elizabeth Patlán Villareal - 22300138</p>
            <p>Irma Rafael Soto - 18100213</p>
            <p>&copy; 2025 - SIEDM</p>
        </footer>
    </div>

    <script src="js/menu.js"></script>
</body>

</html>
