<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
include 'conexion.php';



?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Respaldo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles_desktop.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="principal">
    <div class="wrapper">
        <header class="header">
            <a href="#" class="logo"><img src="./images/logo.png" alt="Logo SIEDM" width="150px" /></a>
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
                    <a href="users.php">Gestión de Usuarios</a>
                <?php endif; ?>
                <a href="citas.php">Gestión de Citas</a>
                <?php if ($idRol == 1 || $idRol == 2 || $idRol == 4): ?>
                    <a href="consultar_historial.php" class="active">Historial Médico</a>
                <?php endif; ?>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <a href="configuración.php">Configuración</a>
                <?php endif; ?>
                <a href="logout.php" class="logout-link">Cerrar sesión</a>
                <span style="font-size: 0.7em;">
                    Usuario: <?php echo $_SESSION["nombreUsuario"]; ?>
                </span>
            </nav>
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        </header>

        <div class="container">
            <?php
if (isset($_GET['ok'])) {
    echo '<div class="alert alert-success">Respaldo generado correctamente.</div>';
}
?>
            <h2>Respaldo de Base de Datos</h2>
            
            <form action="backup.php" method="post" target="_blank" onsubmit="mostrarMensaje()">
                <p>Realiza una copia de seguridad de la base de datos para proteger tus datos importantes.</p>
                <input type="hidden" name="action" value="backup">
                <button type="submit" class="btn btn-primary">Realizar Respaldo</button>
                <button type="button" class="btn-logout" onclick="window.location.href='configuración.php';"><i class="fas fa-arrow-left"></i> Regresar</button>
            </form>
        </div>
        <br>
        <footer class="footer">
            <p>Daniel Cruz Hernández - 22300104</p>
            <p>Nicolás Misael López Cruz - 22300149</p>
            <p>Karen Elizabeth Patlán Villareal - 22300138</p>
            <p>Irma Rafael Soto - 18100213</p>
            <p>&copy; 2025 - SIEDM</p>
        </footer>
    </div>
    <script src="js/menu.js"></script>
    <script>
function mostrarMensaje() {
    setTimeout(function() {
        const contenedor = document.querySelector(".container");
        const alerta = document.createElement("div");
        alerta.className = "alert alert-success";
        alerta.innerHTML = "Respaldo generado exitosamente. Busca en tu carpeta de descargas el archivo SQL con el respaldo de la base de datos.";
        contenedor.prepend(alerta);
    }, 500);
}
</script>

</body>

</html>