<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
include 'conexion.php';

// Obtener el id_paciente de la URL
$id_paciente = $_GET['id_paciente'] ?? null; 
if ($id_paciente === null) {
    die("ID de paciente no proporcionado.");
}

$sql = "SELECT * FROM pacientes WHERE id_paciente = ?";
if ($stmt = $link->prepare($sql)) {
    $stmt->bind_param("i", $id_paciente);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $paciente = $resultado->fetch_assoc();
        
    } else {
        die("Paciente no encontrado.");
    }
    $stmt->close();
} else {
    die("Error en la consulta del paciente: " . $link->error);
}

mysqli_close($link);
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
        </header>

        <div class="container">
            <h2>Modificar Paciente</h2>
            <p>Modifique los datos solicitados en los campos.</p>
            
            <form class="form" action="actualizar_paciente.php" method="POST" enctype="multipart/form-data">
                <!-- Campo oculto para enviar el id_paciente -->
                <input type="hidden" name="id_paciente" value="<?php echo ($id_paciente); ?>">


                <label for="foto">Foto del Paciente:</label>
                <img src="<?php echo htmlspecialchars($paciente['foto']); ?>" style="display: block; margin: 0 auto; width: 150px; height: auto; border-radius: .5px;" alt="Foto del Paciente">
                <br>
                <input type="file" id="foto" name="foto" accept="image/*">
                
                <label for="nombre">Nombre de Paciente:</label>
                <input type="text" id="nombre" name="nombre" oninput="this.value.toUpperCase()" required placeholder="Ingrese el nombre de paciente" value="<?php echo htmlspecialchars($paciente['nombre']); ?>">

                <label for="primer_apellido">Primer apellido:</label>
                <input type="text" id="primer_apellido" name="primer_apellido" oninput="this.value.toUpperCase()" required placeholder="Ingrese Primer Apellido" value="<?php echo htmlspecialchars($paciente['primer_apellido']); ?>">

                <label for="segundo_apellido">Segundo Apellido:</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido" oninput="this.value.toUpperCase()" required placeholder="Ingrese Segundo Apellido" value="<?php echo htmlspecialchars($paciente['segundo_apellido']); ?>">

                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" required placeholder="Ingrese correo electrónico" value="<?php echo htmlspecialchars($paciente['correo']); ?>">

                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" required placeholder="Ingrese teléfono de contacto" pattern="[0-9]{10}" maxlength="10" value="<?php echo htmlspecialchars($paciente['telefono']); ?>">
                
                <label for="curp">CURP:</label>
                <input type="text" id="curp" name="curp" oninput="this.value.toUpperCase()" required placeholder="Ingrese CURP" value="<?php echo htmlspecialchars($paciente['curp']); ?>">

                <label for="edad">Edad:</label>
                <input type="number" id="edad" name="edad" required min="0" max="120" placeholder="Ingrese la edad" value="<?php echo htmlspecialchars($paciente['edad']); ?>">

                <label for="sexo">Sexo:</label>
                <select id="sexo" name="sexo" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Masculino" <?php echo ($paciente['sexo'] == 'MASCULINO') ? 'selected' : ''; ?>>MASCULINO</option>
                    <option value="Femenino" <?php echo ($paciente['sexo'] == 'FEMENINO') ? 'selected' : ''; ?>>FEMENINO</option>
                </select>

                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required value="<?php echo htmlspecialchars($paciente['fecha_nacimiento']); ?>">

                <label for="derechohabiencia">Derechohabiencia:</label>
                <select id="derechohabiencia" name="derechohabiencia" required >
                    <option value="">Seleccione una opción</option>
                    <option value="IMSS" <?php echo ($paciente['derechohabiencia'] == 'IMSS') ? 'selected' : ''; ?>>IMSS</option>
                    <option value="ISSSTE" <?php echo ($paciente['derechohabiencia'] == 'ISSSTE') ? 'selected' : ''; ?>>ISSSTE</option>
                    <option value="INSABI" <?php echo ($paciente['derechohabiencia'] == 'INSABI') ? 'selected' : ''; ?>>INSABI</option>
                    <option value="Privado" <?php echo ($paciente['derechohabiencia'] == 'Privado') ? 'selected' : ''; ?>>Privado</option>
                    <option value="Otro" <?php echo ($paciente['derechohabiencia'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                </select>

                <label for="direccion">Domicilio:</label>
                <input type="text" id="direccion" name="direccion" required placeholder="Ingrese la dirección completa" value="<?php echo htmlspecialchars($paciente['direccion']); ?>">

                <label for="tipo_sangre">Tipo de Sangre:</label>
                <select id="tipo_sangre" name="tipo_sangre" required>
                    <option value="">Seleccione una opción</option>
                    <option value="A+" <?php echo ($paciente['tipo_sangre'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                    <option value="A-" <?php echo ($paciente['tipo_sangre'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                    <option value="B+" <?php echo ($paciente['tipo_sangre'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                    <option value="B-" <?php echo ($paciente['tipo_sangre'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                    <option value="AB+" <?php echo ($paciente['tipo_sangre'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                    <option value="AB-" <?php echo ($paciente['tipo_sangre'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                    <option value="O+" <?php echo ($paciente['tipo_sangre'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                    <option value="O-" <?php echo ($paciente['tipo_sangre'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                </select>

                <label for="religion">Religión:</label>
                <input type="text" id="religion" oninput="this.value.toUpperCase()" name="religion" placeholder="Ingrese religión (opcional)" value="<?php echo htmlspecialchars($paciente['religion']); ?>">

                <label for="ocupacion">Ocupación:</label>
                <input type="text" id="ocupacion" oninput="this.value.toUpperCase()" name="ocupacion" required placeholder="Ingrese ocupación" value="<?php echo htmlspecialchars($paciente['ocupacion']); ?>">

                <label for="alergias">Alergias:</label>
                <input type="text" id="alergias" oninput="this.value.toUpperCase()" name="alergias" placeholder="Ingrese alergias (si aplica)" value="<?php echo htmlspecialchars($paciente['alergias']); ?>">

                <label for="padecimientos">Padecimientos Crónicos:</label>
                <input type="text" id="padecimientos" oninput="this.value.toUpperCase()" name="padecimientos" placeholder="Ingrese padecimientos crónicos (si aplica)" value="<?php echo htmlspecialchars($paciente['padecimientos']); ?>">
                <p></p>
                <div style="text-align: center;">
                <button type="submit" class="btn"> <i class="fas fa-save"></i> Actualizar</button> 
                <button type="button" class="btn-logout" onclick="history.back();"> <i class="fas fa-times"></i> Cancelar</button>
                </div>
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
