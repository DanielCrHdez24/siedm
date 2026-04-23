<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}
$idRol = $_SESSION['idRol'];
if ($idRol != 1) {
    header("location: panel.php");
    exit();
}

$id_usuario = filter_input(INPUT_GET, 'id_usuario', FILTER_VALIDATE_INT); // Utiliza esta variable para obtener los datos relacionados con el usuario
require_once 'conexion.php';

// Recuperar los datos del médico para precargar en el formulario
$sql = "SELECT * FROM usuarios WHERE id_usuario = ? AND id_rol IN (2,3)"; // Asegura que solo se puedan editar médicos o recepcionistas
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $nombre = $row['nombre'];
        $primer_apellido = $row['primer_apellido'];
        $segundo_apellido = $row['segundo_apellido'];
        $correo = $row['correo'];
        $telefono = $row['telefono'];
        // Nota: No cargamos la contraseña por razones de seguridad
    } else {
        header("location: rud_medical.php?error=Usuario+no+encontrado");
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    header("location: rud_medical.php?error=Error+en+la+consulta");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles_desktop.css">
        <link rel="icon" href="images/favicon.png" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar desactivar cuenta</title>
</head>

<body class="principal">
    <div class="wrapper">
        <header class="header">
            <a href="#" class="logo">
                <img src="./images/logo.png" alt="Logo SIEDM" width="150px" />
            </a>
            <nav class="navbar">
                <a href="panel.php">Dashboard</a>
                <?php
                // Verifica el rol y redirige a la página correspondiente
                if ($idRol == 4) {
                    // Si el rol es 4, manda a perfil.php
                    $url = 'perfil.php';
                } elseif ($idRol == 2 || $idRol == 3) {
                    // Si el rol es 2 o 3, manda a perfil_dif.php
                    $url = 'perfil_dif.php';
                } else {
                    // Si no es ninguno de los roles especificados, redirige a una página por defecto o muestra un mensaje
                    $url = 'perfil_dif.php';  // Puedes redirigir a una página de error o algo similar
                }
                ?>

                <a href="<?php echo $url; ?>">Mi Perfil</a>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <!-- Menú para Admin o Médico-->
                    <a href="users.php">Gestión de Usuarios</a>
                <?php endif; ?>

                <a href="citas.php">Gestión de Citas</a>
                <?php if ($idRol == 1 || $idRol == 2 || $idRol == 4): ?>
                    <!-- Menú para Admin, Médico o Paciente-->
                    <a href="consultar_historial.php">Historial Médico</a>
                <?php endif; ?>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <!-- Menú para Admin o Médico-->
                    <a href="configuracion.php">Configuración</a>
                <?php endif; ?>
                <a href="logout.php" class="logout-link">Cerrar sesión</a>
                <span style="font-size: 0.7em;">
                    Usuario: <?php echo $_SESSION["nombreUsuario"]; ?>
                </span>
            </nav>
            <!-- Botón para abrir el menú móvil -->
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        </header>

        <div class="container">
            <h2 style="color: red; font-size: 24px; text-align: center;">¿Está seguro que quiere desactivar la cuenta?</h2>
            <h3 style="color: red; font-size: 18px; text-align: center;">El usuario será desactivado y no podrá acceder al sistema.</h3>
            <br>
            <form class="form" action="borrar_medico.php" method="POST">
                <input style="display: none;" type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($id_usuario); ?>" readonly>

                <label for="nombre">Nombre del Médico o Recepcionista:</label>
                <input style="color: #696969;" type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required readonly>

                <label for="primer_apellido">Primer Apellido:</label>
                <input style="color: #696969" type="text" id="primer_apellido" name="primer_apellido" value="<?php echo htmlspecialchars($primer_apellido); ?>" required readonly>

                <label for="segundo_apellido">Segundo Apellido:</label>
                <input style="color: #696969;" type="text" id="segundo_apellido" name="segundo_apellido" value="<?php echo htmlspecialchars($segundo_apellido); ?>" required readonly>

                <label for="correo">Correo Electrónico:</label>
                <input style="color: #696969;" type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required readonly>

                <label for="telefono">Teléfono:</label>
                <input style="color: #696969;" type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required readonly>
                <p></p>


                <button type="submit" class="btn"
                    onmouseover="this.style.backgroundColor='red'; this.style.color='white';"
                    onmouseout="this.style.backgroundColor='#125873'; this.style.color='white';">
                    <i class="fas fa-trash-alt"></i>
                    Desactivar cuenta
                </button>
                <button type="button" class="btn-logout" onclick="window.location.href='panel.php';"> <i class="fas fa-times"></i> Cancelar</button>
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