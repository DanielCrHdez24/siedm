<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
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

        <div class="container">
            <h2>Gestión de Usuarios!</h2>
            <?php
if (isset($_GET['mensaje'])): 
    $mensaje = htmlspecialchars($_GET['mensaje']);
?>
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>
            <p>
                Ingrese a las opciones para gestionar los datos de los usuarios.
            </p>
            <p></p>
            <div class="card-container">
                <div class="card">
                    <div class="option-icon">
                        <i class="bi bi-house-door"></i>
                    </div>
                    <h3>Inicio</h3>
                    <p>Regresa al panel principal.</p>
                    <a href="panel.php" class="btn">Ir</a>
                </div>
                <?php if ($idRol == 1): ?>
                    <div class="card">
                        <div class="option-icon">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <h3>Agregar Médico o Recepcionista</h3>
                        <p>Crea usuarios Médico o Recepcionista.</p>
                        <a href="add_medical.php" class="btn">Ir</a>
                    </div>
                <?php endif; ?>
                <?php if ($idRol == 1): ?>
                    <div class="card">
                        <div class="option-icon">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <h3>Modificar Médico o recepcionista</h3>
                        <p>Busca, modifica o elimina usuarios.</p>
                        <a href="rud_medical.php" class="btn">Ir</a>
                    </div>
                <?php endif; ?>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <div class="card">
                        <div class="option-icon">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h3>Agregar usuario Paciente</h3>
                        <p>Da de alta usuarios con rol de pacientes.</p>
                        <a href="info_paciente.php" class="btn">Ir</a>
                    </div>
                <?php endif; ?>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <div class="card">
                        <div class="option-icon">
                            <i class="fa-solid fa-user-injured"></i>
                        </div>
                        <h3>Modificar usuario Paciente</h3>
                        <p>Busca, modifica o elimina a paciente.</p>
                        <a href="rud_patient.php" class="btn">Ir</a>
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