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
    <title>Información de Paciente</title>
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
            <!-- Botón para abrir el menú móvil -->
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        </header>

        <div class="container">
            <h1>Agregar paciente.</h1>
            <p>Ingresa los datos del paciente para iniciar el nuevo expediente.</p>


            <!-- Formulario para agregar expediente -->
            <form action="insertar_info_paciente.php" method="POST" enctype="multipart/form-data">

                <label for="foto">Foto del Paciente:</label>
                <input type="file" id="foto" name="foto" accept="image/*" required>

                <!--<label for="clave_expediente">Clave de Expediente:</label>-->
                <!--<input type="text" id="clave_expediente" name="clave_expediente" required placeholder="Ingrese clave de expediente">-->

                <label for="nombre">Nombre de Paciente:</label>
                <input type="text" id="nombre" name="nombre" oninput="this.value = this.value.toUpperCase()" required placeholder="Ingrese el nombre de paciente" pattern="[A-Za-z\s]+" title="El nombre solo puede contener letras y espacios.">
                <p></p>

                <label for="primer_apellido">Primer apellido:</label>
                <input type="text" id="primer_apellido" name="primer_apellido" oninput="this.value = this.value.toUpperCase()" required placeholder="Ingrese Primer Apellido" pattern="[A-Za-z\s]+" title="El primer apellido solo puede contener letras y espacios.">
                <p></p>

                <label for="segundo_apellido">Segundo Apellido:</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido" oninput="this.value = this.value.toUpperCase()" required placeholder="Ingrese Segundo Apellido" pattern="[A-Za-z\s]+" title="El segundo apellido solo puede contener letras y espacios.">
                <p></p>

                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" required placeholder="Ingrese correo electrónico">
                <p></p>

                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" required placeholder="Ingrese teléfono de contacto" pattern="[0-9]{10}" maxlength="10" title="El teléfono debe tener exactamente 10 dígitos.">
                <p></p>    
                <label for="curp">CURP:</label>

                <input type="text" id="curp" name="curp" 
                        maxlength="18"
                        required 
                        pattern="^[A-Z]{4}\d{6}[HM][A-Z]{5}[0-9A-Z]\d$"
                        placeholder="Ej. GARC800101HDFLRS09"
                        style="text-transform: uppercase;"
                        title="Debe tener 18 caracteres en mayúsculas con formato válido.">


                <label for="edad">Edad:</label>
                <input type="number" id="edad" name="edad" required min="0" max="120" placeholder="Ingrese la edad">

                <label for="sexo">Sexo:</label>
                <select id="sexo" name="sexo" required>
                    <option value="">Seleccione una opción</option>
                    <option value="Masculino">MASCULINO</option>
                    <option value="Femenino">FEMENINO</option>
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
                <input type="text" id="direccion" name="direccion" oninput="this.value = this.value.toUpperCase()" required placeholder="Ingrese la dirección completa">

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
                <input type="text" id="religion" name="religion" oninput="this.value = this.value.toUpperCase()" placeholder="Ingrese religión (opcional)">

                <label for="ocupacion">Ocupación:</label>
                <input type="text" id="ocupacion" name="ocupacion" oninput="this.value = this.value.toUpperCase()" required placeholder="Ingrese ocupación">

                <label for="alergias">Alergias:</label>
                <input type="text" id="alergias" name="alergias" oninput="this.value = this.value.toUpperCase()" placeholder="Ingrese alergias (si aplica)">

                <label for="padecimientos">Padecimientos Crónicos:</label>
                <input type="text" id="padecimientos" name="padecimientos" oninput="this.value = this.value.toUpperCase()" placeholder="Ingrese padecimientos crónicos (si aplica)">

                <!-- Campo oculto para pasar el id_usuario -->
                <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">

                <button type="submit" class="btn">Guardar</button>
                <button type="reset" class="btn">Limpiar Datos</button>
                <button type="button" class="btn-logout" onclick="window.location.href='users.php';">Cancelar</button>
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
    <script>
        const curpInput = document.getElementById("curp");

        // Convierte a mayúsculas 
        curpInput.addEventListener("input", function () {
            this.value = this.value.toUpperCase();
        });

        // Validación extra al enviar el formulario
        document.querySelector("form").addEventListener("submit", function (e) {
            const curpRegex = /^[A-Z]{4}\d{6}[HM][A-Z]{5}[0-9A-Z]\d$/;
            if (!curpRegex.test(curpInput.value)) {
            alert("El CURP no es válido. Verifica que tenga 18 caracteres, esté en mayúsculas y cumpla el formato correcto.");
            e.preventDefault(); 
            }
        });
</script>
</body>

</html>