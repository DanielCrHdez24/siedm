<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

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
    <title>Agregar Médico</title>
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
            <h2>Agregar usuario Médico o Recepcionista.</h2>
            <p>
                Ingrese los datos solicitados en los campos.
            </p>
            <p> </p>
            <<form class="form" action="insertar_medico.php" method="POST" onsubmit="return validateForm()">
                <label for="id_rol">Rol del usuario:</label>
                <select id="id_rol" name="id_rol" required>
                    <option value="" disabled selected>Seleccione un rol</option>
                    <option value="2">Médico</option>
                    <option value="3">Recepcionista</option>
                </select>
                <p></p>

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ingrese el nombre de paciente" pattern="[A-Za-z\s]+" title="Solo se permiten letras y espacios">
                <p></p>

                <label for="primer_apellido">Primer apellido:</label>
                <input type="text" id="primer_apellido" name="primer_apellido" required placeholder="Ingrese Primer Apellido" pattern="[A-Za-z\s]+" title="Solo se permiten letras y espacios">
                <p></p>

                <label for="segundo_apellido">Segundo Apellido:</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido" required placeholder="Ingrese Segundo Apellido" pattern="[A-Za-z\s]+" title="Solo se permiten letras y espacios">
                <p></p>

                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" required placeholder="Ingrese correo electrónico" title="Ingrese un correo electrónico válido">
                <p></p>

                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" required placeholder="Ingrese teléfono de contacto" pattern="[0-9]{10}" maxlength="10" title="Ingrese un número de teléfono válido de 10 dígitos">
                <p></p>

                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required placeholder="Ingrese contraseña" minlength="6" title="La contraseña debe tener al menos 6 caracteres">
                <p></p>

                <label for="contrasena2">Confirma la contraseña:</label>
                <input type="password" id="contrasena2" name="contrasena2" required placeholder="Confirma contraseña" minlength="6" title="La contraseña debe tener al menos 6 caracteres">
                <p></p>

                <button type="submit" class="button">Continuar</button>
                <button type="reset" class="button">Limpiar Datos</button>
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