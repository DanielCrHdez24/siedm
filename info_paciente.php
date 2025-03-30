<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}
$idRol = $_SESSION['idRol'];
// Incluir la conexión a la base de datos
include 'conexion.php';

// Obtener y validar el ID del usuario de la URL
$id_usuario = isset($_GET['id_usuario']) ? (int) $_GET['id_usuario'] : 0;

if ($id_usuario <= 0) {
    header("location: panel.php?error=ID+de+usuario+inválido");
    exit();
}

// Consultar los datos del usuario
$sql = "SELECT nombre, primer_apellido, segundo_apellido FROM usuarios WHERE id_usuario = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nombre, $primer_apellido, $segundo_apellido);

    if (!mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        header("location: panel.php?error=Usuario+no+encontrado");
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    error_log("Error en consulta SQL: " . mysqli_error($link));
    header("location: panel.php?error=Error+interno");
    exit();
}

mysqli_close($link);

// Sanitizar el nombre completo para evitar XSS
$nombre_completo = htmlspecialchars($nombre . " " . $primer_apellido . " " . $segundo_apellido, ENT_QUOTES, 'UTF-8');
?>

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
            <h1>Agregar Expediente.</h1>
            <p>Ingresa los datos de <strong><?php echo htmlspecialchars($nombre . ' ' . $primer_apellido . ' ' . $segundo_apellido); ?></strong> para completar el nuevo expediente.</p>


            <!-- Formulario para agregar expediente -->
            <form action="insertar_info_paciente.php" method="POST" enctype="multipart/form-data">

                <label for="foto">Foto del Paciente:</label>
                <input type="file" id="foto" name="foto" accept="image/*" required>

                <label for="clave_expediente">Clave de Expediente:</label>
                <input type="text" id="clave_expediente" name="clave_expediente" required placeholder="Ingrese clave de expediente">

                <label for="curp">CURP:</label>
                <input type="text" id="curp" name="curp" required placeholder="Ingrese CURP">

                <label for="edad">Edad:</label>
                <input type="number" id="edad" name="edad" required min="0" max="120" placeholder="Ingrese la edad">

                <label for="sexo">Sexo:</label>
                <select id="sexo" name="sexo" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                </select>

                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

                <label for="derechohabiencia">Derechohabiencia:</label>
                <select id="derechohabiencia" name="derechohabiencia" required>
                    <option value="">Seleccione una opción</option>
                    <option value="IMSS">IMSS</option>
                    <option value="ISSSTE">ISSSTE</option>
                    <option value="INSABI">INSABI</option>
                    <option value="Privado">Privado</option>
                    <option value="Otro">Otro</option>
                </select>

                <label for="direccion">Domicilio:</label>
                <input type="text" id="direccion" name="direccion" required placeholder="Ingrese la dirección completa">

                <label for="tipo_sangre">Tipo de Sangre:</label>
                <select id="tipo_sangre" name="tipo_sangre" required>
                    <option value="">Seleccione una opción</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>

                <label for="religion">Religión:</label>
                <input type="text" id="religion" name="religion" placeholder="Ingrese religión (opcional)">

                <label for="ocupacion">Ocupación:</label>
                <input type="text" id="ocupacion" name="ocupacion" required placeholder="Ingrese ocupación">

                <label for="alergias">Alergias:</label>
                <input type="text" id="alergias" name="alergias" placeholder="Ingrese alergias (si aplica)">

                <label for="padecimientos">Padecimientos Crónicos:</label>
                <input type="text" id="padecimientos" name="padecimientos" placeholder="Ingrese padecimientos crónicos (si aplica)">

                <!-- Campo oculto para pasar el id_usuario -->
                <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">

                <button type="submit" class="button">Finalizar</button>
                <button type="reset" class="button">Limpiar Datos</button>
                <button type="button" class="button" onclick="window.location.href='users.php';">Cancelar</button>
            </form>

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