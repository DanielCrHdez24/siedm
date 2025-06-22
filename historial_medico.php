<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
include 'conexion.php';

$busqueda = $_POST['busqueda'] ?? '';
$paciente = null;
$citas = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "SELECT * FROM pacientes WHERE nombre LIKE ? OR curp LIKE ? OR id_paciente = ?";
    $stmt = $link->prepare($sql);
    $like = "%" . $busqueda . "%";
    $stmt->bind_param("ssi", $like, $like, $busqueda);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $paciente = $resultado->fetch_assoc();

        // Obtener citas médicas asociadas
        $sql_citas = "SELECT * FROM citas_medicas WHERE id_paciente = ? ORDER BY fecha_cita DESC, hora_cita DESC";
        $stmt_citas = $link->prepare($sql_citas);
        $stmt_citas->bind_param("i", $paciente['id_paciente']);
        $stmt_citas->execute();
        $citas = $stmt_citas->get_result();
    }
}
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
    <title>Modificar paciente</title>
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
            <h2>Historial Médico del Paciente</h2>
            <form method="POST" class="form">
                <input type="text" name="busqueda" placeholder="Buscar por nombre, CURP o ID" required>
                <button type="submit">Buscar</button>
            </form>

            <?php if ($paciente): ?>
                <h3>Datos del Paciente</h3>
                <table class="table">
                    <tr><th>Nombre:</th><td><?= htmlspecialchars($paciente['nombre']) ?> <?= htmlspecialchars($paciente['primer_apellido']) ?> <?= htmlspecialchars($paciente['segundo_apellido']) ?></td></tr>
                    <tr><th>CURP:</th><td><?= htmlspecialchars($paciente['curp']) ?></td></tr>
                    <tr><th>Edad:</th><td><?= htmlspecialchars($paciente['edad']) ?></td></tr>
                    <tr><th>Sexo:</th><td><?= htmlspecialchars($paciente['sexo']) ?></td></tr>
                    <tr><th>Fecha Nacimiento:</th><td><?= htmlspecialchars($paciente['fecha_nacimiento']) ?></td></tr>
                    <tr><th>Teléfono:</th><td><?= htmlspecialchars($paciente['telefono']) ?></td></tr>
                    <tr><th>Derechohabiencia:</th><td><?= htmlspecialchars($paciente['derechohabiencia']) ?></td></tr>
                    <tr><th>Domicilio:</th><td><?= htmlspecialchars($paciente['direccion']) ?></td></tr>
                    <tr><th>Tipo Sangre:</th><td><?= htmlspecialchars($paciente['tipo_sangre']) ?></td></tr>
                    <tr><th>Ocupación:</th><td><?= htmlspecialchars($paciente['ocupacion']) ?></td></tr>
                    <tr><th>Alergias:</th><td><?= htmlspecialchars($paciente['alergias']) ?></td></tr>
                    <tr><th>Padecimientos:</th><td><?= htmlspecialchars($paciente['padecimientos']) ?></td></tr>
                </table>
                <br>
                <h3>Citas Médicas</h3>
                <?php if ($citas->num_rows > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Motivo</th>
                                <th>Diagnostico</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($cita = $citas->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cita['fecha_cita']) ?></td>
                                    <td><?= htmlspecialchars($cita['hora_cita']) ?></td>
                                    <td><?= htmlspecialchars($cita['motivo']) ?></td>
                                    <td><?= htmlspecialchars($cita['diagnostico']) ?></td>
                                    <td><?= htmlspecialchars($cita['estado'] ?? 'Completada') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay citas registradas para este paciente.</p>
                <?php endif; ?>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <p style="color:red;">No se encontró ningún paciente con esos datos.</p>
            <?php endif; ?>
            <br>
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
