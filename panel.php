<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
$id_Usuario = $_SESSION['idUsuario'];
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
    <title>Dashboard</title>
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
            <!-- Botón para abrir el menú móvil -->
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        </header>

        <div class="container">
            <h2>Hola, <?php echo $_SESSION["nombreUsuario"]; ?>!</h2>
            <?php if (isset($_GET['mensaje'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET['mensaje']); ?>
                </div>
            <?php endif; ?>
            <p>
                Ingrese a las opciones del menú para gestionar los datos de los usuarios, citas, historial médico y configuración del sistema.
            </p>
            <p></p>
            <div class="card-container">
                <div class="card">
                    <div class="option-icon">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <h3>Mi perfil</h3>
                    <p>Actualiza tus datos personales o contraseña de acceso.</p>
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
                        $url = 'error.php';  // Puedes redirigir a una página de error o algo similar
                    }
                    ?>

                    <a href="<?php echo $url; ?>" class="btn">Ir</a>
                </div>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <div class="card">
                        <div class="option-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3>Gestión de Usuarios</h3>
                        <p>Crea, modifica y elimina usuarios con diferentes roles.</p>
                        <a href="users.php" class="btn">Ir</a>
                    </div>
                <?php endif; ?>
                <div class="card">
                    <div class="option-icon">
                        <i class="bi bi-calendar"></i>
                    </div>
                    <h3>Gestión de Citas</h3>
                    <p>Crea, modifica y cancela citas para pacientes o médicos.</p>
                    <a href="citas.php" class="btn">Ir</a>
                </div>
                <?php if ($idRol == 1 || $idRol == 2 || $idRol == 4): ?>
                    <div class="card">
                        <div class="option-icon">
                            <i class="bi bi-file-earmark-ruled"></i>
                        </div>
                        <h3>Historial Médico</h3>
                        <p>Crea, modifica y elimina historial médico de pacientes.</p>
                        <a href="historial_medico.php" class="btn">Ir</a>
                    </div>
                <?php endif; ?>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <div class="card">
                        <div class="option-icon">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h3>Configuración</h3>
                        <p>Backup y restauración.</p>
                        <a href="settings.php" class="btn">Ir</a>
                    </div>
                <?php endif; ?>
                <div class="card">
                    <div class="option-icon-logout">
                        <i class="bi bi-box-arrow-left"></i>
                    </div>
                    <h3>Cerrar sesión</h3>
                    <p>Salir del sistema.</p>
                    <a href="logout.php" class="btn-logout">Salir</a>
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