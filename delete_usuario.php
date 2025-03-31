<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}
$idRol = $_SESSION['idRol'];
 // Utiliza esta variable para obtener los datos relacionados con el usuario
 $id_usuario = $_GET['id_usuario'] ?? null; 
if ($id_usuario === null) {
    die("ID de usuario no proporcionado.");
}
include 'conexion.php';

// Recuperar los datos del médico para precargar en el formulario
$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
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
        die("Error: No se encontró el usuario.");
    }

    mysqli_stmt_close($stmt);
} else {
    die("Error en la consulta de usuario: " . mysqli_error($link));
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
    <title>Confrimar eliminar cuenta</title>
</head>

<body class="principal">
    <div class="wrapper">
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
            <!-- Botón para abrir el menú móvil -->
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        </header>

        <div class="container">
        <h2 style="color: red; font-size: 24px; text-align: center;">¿Está seguro que quiere eliminar su cuenta?</h2>
        <h3 style="color: red; font-size: 18px; text-align: center;">Se eliminará de forma permanente y no podrá recuperar la información ni ingresar al sistema.</h3>
            <form class="form" action="borrar_usuario.php" method="POST">
                <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">

                <label for="nombre">Nombre del Médico:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>

                <label for="primer_apellido">Primer Apellido:</label>
                <input type="text" id="primer_apellido" name="primer_apellido" value="<?php echo $primer_apellido; ?>" required>

                <label for="segundo_apellido">Segundo Apellido:</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido" value="<?php echo $segundo_apellido; ?>" required>

                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" value="<?php echo $correo; ?>" required>

                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" value="<?php echo $telefono; ?>" required>

                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" placeholder="Ingrese nueva contraseña (opcional)">

                <label for="contrasena2">Confirma la contraseña:</label>
                <input type="password" id="contrasena2" name="contrasena2" placeholder="Confirma nueva contraseña (opcional)">

                
                <button type="submit" class="button" 
            style="color: white; background: red;"
            onmouseover="this.style.backgroundColor='#ac1a07'; this.style.color='white';" 
            onmouseout="this.style.backgroundColor='red'; this.style.color='white';">
        Eliminar cuenta
    </button>
                <button type="button" class="button" onclick="window.location.href='panel.php';">Cancelar</button>
            </form>
        </div>

        <footer class="footer">
            <p>Daniel Cruz Hernández - 22300104</p>
            <p>Nicolás Misael López Cruz - 22300149</p>
            <p>Karen Elizabeth Patlán Villareal - 22300138</p>
            <p>&copy; 2025 - SIEDM</p>
        </footer>
    </div>

    <script src="js/menu.js"></script>
</body>

</html>
