<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
include 'conexion.php';

// Obtener el id_usuario de la URL
$id_usuario = $_GET['id_usuario'] ?? null; 
if ($id_usuario === null) {
    die("ID de usuario no proporcionado.");
}

// Buscar los datos del usuario
$sql2 = "SELECT * FROM usuarios WHERE id_usuario = ?";
if ($stmt2 = $link->prepare($sql2)) {
    $stmt2->bind_param("i", $id_usuario);
    $stmt2->execute();
    $resultado2 = $stmt2->get_result();

    if ($resultado2->num_rows === 1) {
        $usuario = $resultado2->fetch_assoc();
    } else {
        die("Usuario no encontrado.");
    }
    $stmt2->close();  // Aquí se cierra la declaración correctamente
} else {
    die("Error en la consulta de usuario: " . $link->error);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles_desktop.css">
    <title>Información del usuario</title>
</head>

<body class="principal">
    <div class="wrapper"> <!-- Wrapper para agrupar todo -->
        <header class="header">
            <a href="#" class="logo">
                <img src="./images/logo.png" alt="Logo SIEDM" width="150px" />
            </a>
            <nav class="navbar">
                <a href="panel.php">Dashboard</a>
                <a href="perfil_dif.php">Mi perfil</a>
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
            <title>Información del paciente</title>
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
                            <a href="users.php">Gestión de Usuarios</a>
                        <?php endif; ?>
                        <a href="citas.php">Gestión de Citas</a>
                        <?php if ($idRol == 1 || $idRol == 2 || $idRol == 4): ?>
                            <a href="historial_medico.php">Historial Médico</a>
                        <?php endif; ?>
                        <?php if ($idRol == 1 || $idRol == 2): ?>
                            <a href="configuración.php">Configuración</a>
                        <?php endif; ?>
                        <a href="logout.php" class="logout-link">Cerrar sesión</a>
                    </nav>
                    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
                </header>

                <div class="container">
                    <h2>Información del Médico o Recepcionista</h2>

                    <?php if (isset($_GET['mensaje'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_GET['mensaje']); ?>
                        </div>
                    <?php endif; ?>



                    <!-- Información del médico -->
                    <table class="table" style="font-size:80%;">
                        <tbody>


                            <tr>
                                <th>Nombre</th>
                                <td><?php echo htmlspecialchars($usuario['nombre']) . " " . htmlspecialchars($usuario['primer_apellido']) . " " . htmlspecialchars($usuario['segundo_apellido']); ?></td>
                                <th>E-mail</th>
                                <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                <th>Teléfono</th>
                                <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                            </tr>
                            <tr>
                                <th>Fecha de Registro</th>
                                <td><?php echo htmlspecialchars($usuario['fecha_registro']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <P></P>
                    <h2 class="text-center">Opciones del Médico</h2>

                    <div class="options-container">

    <div class="add-option">
        <a href="update_medical.php?id_usuario=<?php echo $usuario['id_usuario']; ?>"><i class="fa-solid fa-user-edit"></i> Modificar Información de usuario</a>
    </div>
    <?php if ($idRol == 1): ?>
    <div class="add-option">
        <a href="delete_usuario.php?id_usuario=<?php echo $usuario['id_usuario']; ?>" onclick="return confirm('¿Estás seguro de eliminar a este usuario?');">
            <i class="fa-solid fa-user-times"></i> Eliminar usuario
        </a>
    </div>
<?php else: ?>
    <div class="add-option">
        <a href="delete_medical.php?id_usuario=<?php echo $usuario['id_usuario']; ?>" onclick="return confirm('¿Estás seguro de eliminar a este usuario?');">
            <i class="fa-solid fa-user-times"></i> Eliminar usuario
        </a>
    </div>
<?php endif; ?>
    

    <div class="add-option">
        <a href="panel.php"><i class="fa-solid fa-house-chimney"></i> Inicio</a>
    </div>
</div>
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
