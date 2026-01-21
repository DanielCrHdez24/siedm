<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
include 'conexion.php';

$cita_id_get = $_GET['id_cita'] ?? null;

if ($cita_id_get === null) {
    // Si no viene el id_cita por GET, redirigir a la página de citas
    header("location: citas.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $cita_id_get !== null) {

    $sql_citas = "SELECT c.*, p.*
              FROM citas_medicas c
              INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
              WHERE c.id_cita = ?";
    $stmt_citas = $link->prepare($sql_citas);
    $stmt_citas->bind_param("i", $cita_id_get);
    $stmt_citas->execute();
    $result = $stmt_citas->get_result();
    $citas = $result->fetch_assoc();
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Receta cita</title>
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
            <h2>Cita Médica</h2>
          
                <h3>Datos del Paciente</h3>

                <table class="table" style="font-size:80%;">
                    <tbody>
                        <tr>

                            <!-- Aquí la imagen ocupa toda una columna -->
                            <td rowspan="7" style="text-align: center; vertical-align: middle;">

                                <img src="<?php echo htmlspecialchars($citas['foto']); ?>" style="display: block; margin: 0 auto;">

                            </td>
                        </tr>


                        <tr>
                            
                            <th>Nombre</th>
                            <td><?php echo htmlspecialchars($citas['nombre']) . " " . htmlspecialchars($citas['primer_apellido']) . " " . htmlspecialchars($citas['segundo_apellido']); ?></td>
                            <th>CURP</th>
                            <td><?php echo htmlspecialchars($citas['curp']); ?></td>
                            <th>Edad</th>
                            <td> <?php
                                    $fecha_nac = new DateTime($citas['fecha_nacimiento']);
                                    $hoy = new DateTime();
                                    $edad = $hoy->diff($fecha_nac)->y;
                                    echo $edad;
                                    ?> AÑOS</td>

                        </tr>
                        <tr>
                            <th>Teléfono</th>
                            <td><?php echo htmlspecialchars($citas['telefono']); ?></td>
                            <th>Fecha de Nacimiento</th>
                            <td><?php echo htmlspecialchars($citas['fecha_nacimiento']); ?></td>
                            <th>Sexo</th>
                            <td><?php echo htmlspecialchars($citas['sexo']); ?></td>

                        </tr>
                        <tr>
                            <th>E-mail</th>
                            <td><?php echo htmlspecialchars($citas['correo']); ?></td>
                            <th>Derechohabiencia</th>
                            <td><?php echo htmlspecialchars($citas['derechohabiencia']); ?></td>
                            <th>Tipo de Sangre</th>
                            <td><?php echo htmlspecialchars($citas['tipo_sangre']); ?></td>
                        </tr>
                        <tr>
                            <th>Dirección</th>
                            <td colspan="3"><?php echo htmlspecialchars($citas['direccion']); ?></td>
                            <th>Religión</th>
                            <td><?php echo htmlspecialchars($citas['religion']); ?></td>
                        </tr>
                        <tr>
                            <th>Ocupación</th>
                            <td><?php echo htmlspecialchars($citas['ocupacion']); ?></td>
                            <th>Alergias</th>
                            <td><?php echo htmlspecialchars($citas['alergias']); ?></td>
                            <th>Padecimientos Crónicos</th>
                            <td><?php echo htmlspecialchars($citas['padecimientos']); ?></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <h3>Receta</h3>
                <table class="table" style="font-size:80%;">
                    <tbody>
                        <tr>
                            <th>Peso</th>
                            <td><?php echo htmlspecialchars($citas['peso']); ?> kg</td>
                            <th>Talla</th>
                            <td><?php echo htmlspecialchars($citas['talla']); ?> cm</td>
                            <th>Temperatura</th>
                            <td><?php echo htmlspecialchars($citas['temperatura']); ?> °C</td>
                        </tr>
                        <tr>
                            <th>Diagnóstico</th>
                            <td colspan="5"><?php echo htmlspecialchars($citas['diagnostico']); ?></td>
                        </tr>
                        <tr>
                            <th>Indicaciones</th>
                            <td colspan="5"><?php echo htmlspecialchars($citas['indicaciones']); ?></td>
                        </tr>
                        <tr>
                            <th>Recomendaciones</th>
                            <td colspan="5"><?php echo htmlspecialchars($citas['recomendaciones']); ?></td>
                        </tr>
                    </tbody>
                </table>
                <br>

                <form action="generar_pdf_receta.php" method="POST" target="_blank">
                    <input type="hidden" name="id_cita" value="<?php echo htmlspecialchars($citas['id_cita']); ?>">    
                    <button type="submit" class="btn">
                        <i class="fas fa-file-pdf"></i> Receta PDF</button>
                        <button type="button" class="btn" onclick="window.location.href='citas.php'">
                        <i class="fas fa-arrow-left"></i> Volver a Citas</button>
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
</body>

</html>