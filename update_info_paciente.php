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
}

// Consultar los datos del expediente del paciente
$sql = "SELECT clave_expediente, curp, edad, sexo, fecha_nacimiento, derechohabiencia, direccion, 
               tipo_sangre, religion, ocupacion, alergias, padecimientos, id_paciente 
        FROM pacientes WHERE id_usuario = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $clave_expediente, $curp, $edad, $sexo, $fecha_nacimiento, 
                            $derechohabiencia, $direccion, $tipo_sangre, $religion, 
                            $ocupacion, $alergias, $padecimientos, $id_paciente);
    
    if (!mysqli_stmt_fetch($stmt)) {
        // Si no hay expediente, se asignan valores vacíos para evitar errores
        $clave_expediente = $curp = $sexo = $fecha_nacimiento = $derechohabiencia = "";
        $direccion = $tipo_sangre = $religion = $ocupacion = $alergias = $padecimientos = "";
        $edad = "";
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($link);

// Sanitizar el nombre completo para evitar XSS
$nombre_completo = htmlspecialchars($nombre . " " . $primer_apellido . " " . $segundo_apellido, ENT_QUOTES, 'UTF-8');
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
    <title>Modificar Paciente</title>
</head>

<body class="principal">
    <div class="wrapper"> <!-- Wrapper para agrupar todo -->
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
        </header>
    <div class="container">
        <h1>Editar Expediente</h1>
        <p>Modifica los datos de <strong><?php echo $nombre_completo; ?></strong>.</p>

        <form action="actualizar_info_paciente.php" method="POST" enctype="multipart/form-data">
            <label for="foto">Foto del Paciente:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <label for="clave_expediente">Clave de Expediente:</label>
            <input type="text" id="clave_expediente" name="clave_expediente" required 
                   value="<?php echo htmlspecialchars($clave_expediente); ?>" readonly>

            <label for="curp">CURP:</label>
            <input type="text" id="curp" name="curp" required 
                   value="<?php echo htmlspecialchars($curp); ?>">

            <label for="edad">Edad:</label>
            <input type="number" id="edad" name="edad" required min="0" max="120"
                   value="<?php echo htmlspecialchars($edad); ?>">

            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" required>
                <option value="">Seleccione una opción</option>
                <option value="Masculino" <?php echo ($sexo == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                <option value="Femenino" <?php echo ($sexo == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
            </select>

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required 
                   value="<?php echo htmlspecialchars($fecha_nacimiento); ?>">

            <label for="derechohabiencia">Derechohabiencia:</label>
            <select id="derechohabiencia" name="derechohabiencia" required>
                <option value="">Seleccione una opción</option>
                <option value="IMSS" <?php echo ($derechohabiencia == 'IMSS') ? 'selected' : ''; ?>>IMSS</option>
                <option value="ISSSTE" <?php echo ($derechohabiencia == 'ISSSTE') ? 'selected' : ''; ?>>ISSSTE</option>
                <option value="INSABI" <?php echo ($derechohabiencia == 'INSABI') ? 'selected' : ''; ?>>INSABI</option>
                <option value="Privado" <?php echo ($derechohabiencia == 'Privado') ? 'selected' : ''; ?>>Privado</option>
                <option value="Otro" <?php echo ($derechohabiencia == 'Otro') ? 'selected' : ''; ?>>Otro</option>
            </select>

            <label for="direccion">Domicilio:</label>
            <input type="text" id="direccion" name="direccion" required 
                   value="<?php echo htmlspecialchars($direccion); ?>">

            <label for="tipo_sangre">Tipo de Sangre:</label>
            <select id="tipo_sangre" name="tipo_sangre" required>
                <option value="">Seleccione una opción</option>
                <?php
                $tipos_sangre = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];
                foreach ($tipos_sangre as $tipo) {
                    echo "<option value='$tipo' " . ($tipo_sangre == $tipo ? "selected" : "") . ">$tipo</option>";
                }
                ?>
            </select>

            <label for="religion">Religión:</label>
            <input type="text" id="religion" name="religion" 
                   value="<?php echo htmlspecialchars($religion); ?>">

            <label for="ocupacion">Ocupación:</label>
            <input type="text" id="ocupacion" name="ocupacion" required 
                   value="<?php echo htmlspecialchars($ocupacion); ?>">

            <label for="alergias">Alergias:</label>
            <input type="text" id="alergias" name="alergias" 
                   value="<?php echo htmlspecialchars($alergias); ?>">

            <label for="padecimientos">Padecimientos Crónicos:</label>
            <input type="text" id="padecimientos" name="padecimientos" 
                   value="<?php echo htmlspecialchars($padecimientos); ?>">

            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
            <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">

            <button type="submit" class="button">Guardar Cambios</button>
            <button type="reset" class="button">Restablecer</button>
            <button type="button" class="button" onclick="window.location.href='users.php';">Cancelar</button>
        </form>
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