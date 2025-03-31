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
        $paciente = $resultado->fetch_assoc();
        $id_usuario = $paciente['id_usuario'];  // Obtener el id_usuario del paciente

        // Ahora buscamos los datos del usuario
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
            $stmt2->close();
        } else {
            die("Error en la consulta de usuario: " . $link->error);
        }
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
    <title>Confirmación eliminar paciente</title>
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
            <h2 style="color: red; font-size: 24px; text-align: center;">¿Está seguro que quiere eliminar al paciente?</h2>
            <h3 style="color: red; font-size: 18px; text-align: center;">Se eliminará de forma permanente el paciente y no podrá recuperar la información.</h3>

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
                        <th>Clave de Expediente</th>
                        <td><?php echo htmlspecialchars($paciente['clave_expediente']); ?></td>
                        <th>Nombre</th>
                        <td><?php echo htmlspecialchars($usuario['nombre']) . " " . htmlspecialchars($usuario['primer_apellido']) . " " . htmlspecialchars($usuario['segundo_apellido']); ?></td>
                        <th>CURP</th>
                        <td><?php echo htmlspecialchars($paciente['curp']); ?></td>
                    </tr>
                    <tr>
                        <th>Edad</th>
                        <td><?php echo htmlspecialchars($paciente['edad']); ?></td>
                        <th>Sexo</th>
                        <td><?php echo htmlspecialchars($paciente['sexo']); ?></td>
                        <th>Fecha de Nacimiento</th>
                        <td><?php echo htmlspecialchars($paciente['fecha_nacimiento']); ?></td>
                    </tr>
                    <tr>
                        <th>E-mail</th>
                        <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                        <th>Teléfono</th>
                        <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                        <th>Derechohabiencia</th>
                        <td><?php echo htmlspecialchars($paciente['derechohabiencia']); ?></td>
                    </tr>
                    <tr>
                        <th>Dirección</th>
                        <td colspan="5"><?php echo htmlspecialchars($paciente['direccion']); ?></td>
                    </tr>
                    <tr>
                        <th>Tipo de Sangre</th>
                        <td><?php echo htmlspecialchars($paciente['tipo_sangre']); ?></td>
                        <th>Religión</th>
                        <td><?php echo htmlspecialchars($paciente['religion']); ?></td>
                        <th>Ocupación</th>
                        <td><?php echo htmlspecialchars($paciente['ocupacion']); ?></td>
                    </tr>
                    <tr>
                        <th>Alergias</th>
                        <td><?php echo htmlspecialchars($paciente['alergias']); ?></td>
                        <th>Padecimientos Crónicos</th>
                        <td><?php echo htmlspecialchars($paciente['padecimientos']); ?></td>
                        <th>Fecha de Registro</th>
                        <td><?php echo htmlspecialchars($paciente['fecha_registro']); ?></td>
                    </tr>
                </tbody>
            </table>
            <P></P>
            <h2 class="text-center">Opciones del Paciente</h2>

            <div class="options-container">

                <div class="add-option">
                    <a href="borrar_paciente.php?id_paciente=<?php echo $paciente['id_paciente']; ?>" onclick="return confirm('Se eliminara este paciente de forma permanente y no podrá recuperar su infromación!');"><i class="fa-solid fa-user-times"></i> Eliminar paciente</a>
                </div>

                <div class="add-option">
                    <a href="paciente.php?id_paciente=<?php echo $paciente['id_paciente']; ?>"><i class="fa-solid fa-xmark"></i> Cancelar</a>
                </div>

            </div>
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