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
    <title>Configuración del Sistema - SIEDM</title>
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
            <h1 class="title">Configuración del Sistema</h1>
            <div class="cnt-form">
                <div class="settings-section">
                    <h2>Realizar Backup de Archivos</h2>
                    <p>Crea una copia de seguridad de los archivos importantes del sistema (código, imágenes, documentos, etc.).</p>
                    <button class="button" id="backupButton">Iniciar Backup</button>
                    <div id="backupStatus" class="status-message"></div>
                </div>

                <div class="settings-section">
                    <h2>Restaurar Archivos del Sistema</h2>
                    <p>Restaura los archivos del sistema a partir de una copia de seguridad ZIP existente.</p>
                    <div class="form-group">
                        <label for="restoreFileInput">Seleccionar archivo ZIP:</label>
                        <input type="file" id="restoreFileInput" accept=".zip" class="file-input">
                    </div>
                    <button class="button" id="restoreButton" disabled>Restaurar desde Archivo</button>
                    <div id="restoreStatus" class="status-message"></div>
                </div>

                <div class="settings-section">
                    <h2>Configuración de Backup Automático de Archivos</h2>
                    <p>Programa backups automáticos de archivos para mantener tus datos seguros.</p>
                    <div class="form-group">
                        <label for="backupFrequency">Frecuencia:</label>
                        <select id="backupFrequency" class="form-select">
                            <option value="daily">Diario</option>
                            <option value="weekly">Semanal</option>
                            <option value="monthly">Mensual</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="backupTime">Hora (HH:MM):</label>
                        <input type="time" id="backupTime" value="02:00" class="form-input-time">
                    </div>
                    <button class="button" id="saveAutoBackupConfig">Guardar Configuración</button>
                    <div id="autoBackupStatus" class="status-message"></div>
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