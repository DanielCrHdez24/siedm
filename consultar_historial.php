<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
include 'conexion.php';

$busqueda = $_POST['busqueda'] ?? '';
$pacientes = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($busqueda)) {
        $sql = "SELECT * 
                FROM pacientes 
                WHERE nombre LIKE ? OR primer_apellido LIKE ? OR segundo_apellido LIKE ? OR curp LIKE ?
                ORDER BY curp ASC";
        $stmt = $link->prepare($sql);
        $busqueda_param = "%$busqueda%";
        $stmt->bind_param("ssss", $busqueda_param, $busqueda_param, $busqueda_param, $busqueda_param);
        $stmt->execute();
        $pacientes = $stmt->get_result();
        $stmt->close();
    
}else{
    $pacientes = $link->query("SELECT * FROM pacientes WHERE 1=0"); 
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Consultar Paciente</title>
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
            <h2>Seleccionar paciente</h2>
            <?php if (isset($_GET['mensaje'])): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;">
                    <?= htmlspecialchars($_GET['mensaje']) ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="form">
                <input type="text" id="inputBuscar" name="busqueda" oninput="this.value = this.value.toUpperCase()" placeholder="Buscar por Fecha CURP, Nombre o Apellido" required>
                <button type="submit" class="btn"><i class="fas fa-search"></i> Buscar</button>
                <button type="button" class="btn"
                    onclick="document.getElementById('inputBuscar').value='';">
                    <i class="fas fa-eraser"></i> Borrar
                </button>

                <button type="button" class="btn"
                    onclick="window.location.href='panel.php';">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
            </form>

           
                <h3>Pacientes</h3>
                <?php if ($pacientes->num_rows > 0): ?>
                    <table class="table" style="font-size:80%;">
                        <thead>
                            <tr>
                                
                                <th>Paciente</th>
                                <th>CURP</th>
                                <th>Edad</th>
                                <th>Sexo</th>
                                <th>Fecha Nac.</th>
                                <th>Derechohabiencia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($paciente = $pacientes->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($paciente['nombre']. ' ' . $paciente['primer_apellido'] . ' ' . $paciente['segundo_apellido']) ?></td>
                                    <td><?= htmlspecialchars($paciente['curp']) ?></td>
                                    <td> <?php
                                    $fecha_nac = new DateTime($paciente['fecha_nacimiento']);
                                    $hoy = new DateTime();
                                    $edad = $hoy->diff($fecha_nac)->y;
                                    echo $edad;
                                    ?> AÑOS</td>
                                    <td><?= htmlspecialchars($paciente['sexo']) ?></td>
                                    <td><?= htmlspecialchars($paciente['fecha_nacimiento']) ?></td>
                                    <td><?= htmlspecialchars($paciente['derechohabiencia']) ?></td>
                                    <td>
                                        <a href="historial.php?id_paciente=<?= $paciente['id_paciente'] ?>">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay pacientes registrados con esos criterios.</p>
                <?php endif; ?>
                <br>

                <form action="generar_pdf.php" method="post" target="_blank" style="text-align: center;">
                    <input type="hidden" name="id_paciente" value="<?= $paciente['id_paciente'] ?>">
                    <button type="button" class="btn-logout" onclick="window.location.href='citas.php';"><i class="fas fa-arrow-left"></i> Volver</button>
                </form>
                    
            <br>

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
</body>

</html>
