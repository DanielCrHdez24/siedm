<?php
session_start();
require_once "conexion.php"; // Archivo de conexión a la base de datos

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];

// Consulta para obtener la lista de pacientes con su foto y clave de expediente
$query = "SELECT id_paciente, clave_expediente, foto FROM pacientes";
$result = mysqli_query($link, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($link));
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles_desktop.css">
    <title>Agendar Cita</title>
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
                <a href="citas.php">Gestión de Citas</a>
                <a href="historial_medico.php">Historial Médico</a>
                <a href="logout.php" class="logout-link">Cerrar sesión</a>
            </nav>
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        </header>

        <div class="container">
            <h2>Agendar Cita Médica</h2>

            <form class="form" action="insertar_cita.php" method="POST">
                <label for="paciente">Seleccione un paciente:</label>
                <select id="paciente" name="id_paciente" required>
                    <option value="">Seleccione un paciente</option>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <option value="<?php echo $row['id_paciente']; ?>">
                            <?php echo $row['clave_expediente']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <p></p>

                <label for="foto">Foto de identificación:</label>
                <div id="foto-container">
                    <img id="foto-preview" src="images/default.png" alt="Foto del paciente" width="150">
                </div>
                <p></p>

                <label for="fecha_cita">Fecha y hora de la cita:</label>
                <input type="datetime-local" id="fecha_cita" name="fecha_cita" required>
                <p></p>

                <label for="motivo">Motivo de la cita:</label>
                <textarea id="motivo" name="motivo" required placeholder="Describa el motivo de la consulta"></textarea>
                <p></p>

                <input type="hidden" name="estado" value="Pendiente">
                <button type="submit" class="btn">Agendar Cita</button>
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
        // Muestra la foto del paciente al seleccionar en el dropdown
        document.getElementById("paciente").addEventListener("change", function() {
            let selectedId = this.value;
            let fotoPreview = document.getElementById("foto-preview");

            if (selectedId) {
                fetch("obtener_foto_paciente.php?id_paciente=" + selectedId)
                    .then(response => response.json())
                    .then(data => {
                        fotoPreview.src = data.foto ? "uploads/" + data.foto : "images/default.png";
                    })
                    .catch(error => console.error("Error:", error));
            } else {
                fotoPreview.src = "images/default.png";
            }
        });
    </script>
</body>

</html>
