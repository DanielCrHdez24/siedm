<?php
session_start();

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
    <title>Gestión de Citas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles_desktop.css">
</head>

<body class="principal">
    <div class="wrapper">
        <header class="header">
            <a href="#" class="logo">
                <img src="./images/logo.png" alt="Logo SIEDM" width="150px" />
            </a>
            <nav class="navbar">
                <a href="panel.php">Dashboard</a>
                <a href="perfil_dif.php">Mi perfil</a>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <a href="users.php">Gestión de Usuarios</a>
                <?php endif; ?>
                <a href="citas.php" class="active">Gestión de Citas</a>
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
            <h2>Gestión de Citas Médicas</h2>
            <div class="card-container">
                <div class="card">
                    <i class="fa-solid fa-calendar-plus option-icon"></i>
                    <h3>Agendar Cita</h3>
                    <p>Programa una nueva consulta médica de forma rápida.</p>
                    <a href="agendar_cita.php" class="btn"><i class="fas fa-calendar-plus"></i> Agendar</a>
                </div>

                <div class="card">
                    <i class="fa-solid fa-user-md option-icon"></i>
                    <h3>Atender Citas</h3>
                    <p>En esta sección se atienden las consultas médicas programadas.</p>
                    <a href="atender_cita.php" class="btn"><i class="fas fa-user-md"></i> Atender</a>
                </div>

                <div class="card">
                    <i class="fa-solid fa-magnifying-glass option-icon"></i>
                    <h3>Consultar Citas</h3>
                    <p>Visualiza todas las citas que han sido programadas.</p>
                    <a href="consultar_cita.php" class="btn"><i class="fas fa-magnifying-glass"></i> Consultar</a>
                </div>

                <div class="card">
                    <i class="fa-solid fa-calendar-xmark option-icon"></i>
                    <h3>Cancelar Cita</h3>
                    <p>Elimina una cita programada que ya no será atendida.</p>
                    <a href="cancelar_cita.php" class="btn-logout"><i class="fas fa-calendar-xmark"></i> Cancelar</a>
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
