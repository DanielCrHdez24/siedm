<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}
$idRol = $_SESSION['idRol'];
include 'conexion.php';

// Validar y recibir el ID del paciente
$id_paciente = filter_input(INPUT_GET, 'id_paciente', FILTER_VALIDATE_INT);

if (!$id_paciente) {
    die("ID de paciente no válido o no proporcionado.");
    
}

// Consulta de datos del paciente junto con el id_usuario
$sql = "SELECT * FROM pacientes WHERE id_paciente = ?";
if ($stmt = $link->prepare($sql)) {
    $stmt->bind_param("i", $id_paciente);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $paciente = $resultado->fetch_assoc(); // Obtener el id_usuario del paciente

        // Ahora buscamos los datos del usuario
        
    } else {
        die("Paciente no encontrado.");
    }
    $stmt->close();
} else {
    die("Error en la consulta del paciente: " . $link->error);
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
    <title>Información del paciente</title>
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
                            <a href="consultar_historial.php">Historial Médico</a>
                        <?php endif; ?>
                        <?php if ($idRol == 1 || $idRol == 2): ?>
                            <a href="configuración.php">Configuración</a>
                        <?php endif; ?>
                        <a href="logout.php" class="logout-link">Cerrar sesión</a>
                    </nav>
                    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
                </header>

                <div class="container">
                    <h2>Información del Paciente</h2>

                    <?php if (isset($_GET['mensaje'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_GET['mensaje']); ?>
                        </div>
                    <?php endif; ?>



                    <!-- Información del paciente -->
                    <table class="table" style="font-size:80%;">
                        <tbody>
                            <tr>

                                <!-- Aquí la imagen ocupa toda una columna -->
                                <td rowspan="7" style="text-align: center; vertical-align: middle;">

        <img src="<?php echo htmlspecialchars($paciente['foto']); ?>" style="display: block; margin: 0 auto;">

    </td>
                            </tr>


                            <tr>
                                <!--<th>Clave de Expediente</th>
                                <td><?php #echo htmlspecialchars($paciente['clave_expediente']); ?></td>-->
                                <th>Nombre</th>
                                <td><?php echo htmlspecialchars($paciente['nombre']) . " " . htmlspecialchars($paciente['primer_apellido']) . " " . htmlspecialchars($paciente['segundo_apellido']); ?></td>
                                <th>CURP</th>
                                <td><?php echo htmlspecialchars($paciente['curp']); ?></td>
                                <th>Edad</th>
                                <td><?php echo htmlspecialchars($paciente['edad']); ?></td>
                                
                            </tr>
                            <tr>
                                <th>Teléfono</th>
                                <td><?php echo htmlspecialchars($paciente['telefono']); ?></td>
                                <th>Fecha de Nacimiento</th>
                                <td><?php echo htmlspecialchars($paciente['fecha_nacimiento']); ?></td>
                                <th>Sexo</th>
                                <td><?php echo htmlspecialchars($paciente['sexo']); ?></td>
                                
                            </tr>
                            <tr>
                                <th>E-mail</th>
                                <td><?php echo htmlspecialchars($paciente['correo']); ?></td>
                                <th>Derechohabiencia</th>
                                <td><?php echo htmlspecialchars($paciente['derechohabiencia']); ?></td>
                                <th>Tipo de Sangre</th>
                                <td><?php echo htmlspecialchars($paciente['tipo_sangre']); ?></td>
                            </tr>
                            <tr>
                                <th>Dirección</th>
                                <td colspan="3"><?php echo htmlspecialchars($paciente['direccion']); ?></td>
                                <th>Religión</th>
                                <td><?php echo htmlspecialchars($paciente['religion']); ?></td>
                            </tr>
                            <tr>
                                <th>Ocupación</th>
                                <td><?php echo htmlspecialchars($paciente['ocupacion']); ?></td>
                                <th>Alergias</th>
                                <td><?php echo htmlspecialchars($paciente['alergias']); ?></td>
                                <th>Padecimientos Crónicos</th>
                                <td><?php echo htmlspecialchars($paciente['padecimientos']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <P></P>
                    <h2 class="text-center">Opciones del Paciente</h2>

                    <div class="options-container">
    <div class="add-option">
        <a href="agendar_cita.php?id_paciente=<?php echo $paciente['id_paciente']; ?>"><i class="fa-solid fa-calendar-plus"></i> Agendar una cita</a>
    </div>

    <div class="add-option">
        <a href="update_patient.php?id_paciente=<?php echo $paciente['id_paciente']; ?>"><i class="fa-solid fa-user-edit"></i> Modificar paciente</a>
    </div>

    <div class="add-option">
        <a href="delete_patient.php?id_paciente=<?php echo $paciente['id_paciente']; ?>" onclick="return confirm('¿Estás seguro de eliminar a este paciente?');"><i class="fa-solid fa-user-times"></i> Eliminar paciente</a>
    </div>

    <div class="add-option">
        <a href="historial_medico.php?id_paciente=<?php echo $paciente['id_paciente']; ?>"><i class="fa-solid fa-file-medical-alt"></i> Ver historial médico</a>
    </div>
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
