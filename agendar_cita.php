<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];

$busqueda = "";
$resultados = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["buscar"])) {
    $busqueda = trim($_POST["buscar"]);
    $sql = "SELECT id_paciente, nombre, primer_apellido, segundo_apellido, curp, fecha_nacimiento 
            FROM pacientes 
            WHERE id_paciente LIKE ? OR nombre LIKE ? OR curp LIKE ?";
    $stmt = $link->prepare($sql);
    $param = "%" . $busqueda . "%";
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $resultados = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agendar Cita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles_desktop.css">
</head>

<body class="principal">
    <div class="wrapper">
        <header class="header">
            <a href="#" class="logo">
                <img src="./images/logo.png" alt="Logo SIEDM" width="150px" />
            </a>
            <nav class="navbar">
                <a href="panel.php">Dashboard</a>
                <a href="perfil_dif.php">Mi perfil</a>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <a href="users.php">Gestión de Usuarios</a>
                <?php endif; ?>
                <a href="citas.php">Gestión de Citas</a>
                <?php if ($idRol == 1 || $idRol == 2 || $idRol == 4): ?>
                    <a href="historial_medico.php" class="active">Historial Médico</a>
                <?php endif; ?>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <a href="configuración.php">Configuración</a>
                <?php endif; ?>
                <a href="logout.php" class="logout-link">Cerrar sesión</a>
            </nav>
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        </header>

        <div class="container">
            <h2>Agendar Cita Médica</h2>
            <p>Buscar paciente por CURP, nombre o ID:</p>
                    <?php if (isset($_GET['mensaje'])): ?>
    <div class="alert-success">
        <?= htmlspecialchars($_GET['mensaje']); ?>
    </div>
<?php endif; ?>

            <!-- Formulario de búsqueda -->
            <form method="POST">
                <input type="text" name="buscar" oninput="this.value = this.value.toUpperCase()" value="<?= htmlspecialchars($busqueda); ?>" placeholder="Ej. CURP, Juan, 123">
                <button type="submit">Buscar</button>
            </form>

            <!-- Mostrar resultados -->
            <?php if (!empty($resultados) && $resultados->num_rows > 0): ?>
                <form method="POST" action="insertar_cita.php">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>CURP</th>
                                <th>Fecha de nacimiento</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Médico</th>
                                <th>Motivo</th>
                                <th>Agendar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($p = $resultados->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p["nombre"] . ' ' . $p["primer_apellido"] . ' ' . $p["segundo_apellido"]); ?></td>
                                    <td><?= htmlspecialchars($p["curp"]); ?></td>
                                    <td><?= htmlspecialchars($p["fecha_nacimiento"]); ?></td>
                                    <td>
                                        <input type="date" name="fecha_cita" required>
                                    </td>
                                    <td>
                                        <input type="time" name="hora_cita" required>
                                    </td>
                                    <td>
                                        <select name="id_usuario" id="">
                                            <?php
                                            // Aquí debes obtener los usuarios disponibles para la cita
                                            $sql = "SELECT id_usuario, nombre, primer_apellido, segundo_apellido FROM usuarios WHERE id_rol = 2";
                                            $resultado_usuarios = $link->query($sql);

                                            if ($resultado_usuarios->num_rows > 0) {
                                                while ($usuario = $resultado_usuarios->fetch_assoc()) {
                                                    echo '<option value="' . $usuario["id_usuario"] . '">' . htmlspecialchars($usuario["nombre"] . ' ' . $usuario["primer_apellido"] . ' ' . $usuario["segundo_apellido"]) . '</option>';
                                                }
                                            } else {
                                                echo '<option value="">No hay médicos disponibles</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="motivo" required placeholder="Motivo">
                                    </td>
                                    <td style=" text-align: center;">
                                        <button type="submit" name="id_paciente" value="<?= $p['id_paciente'] ?>" class="btn">
                                            <i class="fas fa-calendar-plus"></i> Agendar
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <p class="alert-error">No se encontraron pacientes con esa búsqueda.</p>
            <?php endif; ?>
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
