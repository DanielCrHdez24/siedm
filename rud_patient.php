<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
require_once "conexion.php"; // Conexión a la base de datos

// Inicializa variables
$busqueda = "";
$resultados = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["buscar"])) {
    $busqueda = trim($_POST["buscar"]);

    // Consulta para buscar por ID, Nombre o CURP
    $sql = "SELECT id_paciente, nombre, primer_apellido, segundo_apellido, curp, fecha_nacimiento FROM pacientes 
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
            <h2>Modificación de Usuarios!</h2>
            <p>Ingrese Nombre, CURP o ID de Paciente.</p>

            <!-- Formulario de búsqueda -->
            <form method="POST">
                <input type="text" name="buscar" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar paciente...">
                <button type="submit">Buscar</button>
            </form>

            <!-- Resultados de búsqueda -->
<?php if (!empty($resultados) && $resultados->num_rows > 0): ?>
    <table border="1" cellpadding="10" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>CURP</th>
                <th>Fecha de Nacimiento</th>
                <th>Acciones</th> <!-- Nueva columna -->
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $resultados->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila["id_paciente"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["nombre"] . ' ' . $fila["primer_apellido"] . ' ' . $fila["segundo_apellido"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["curp"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["fecha_nacimiento"]); ?></td>
                    <td>
                        <a href="update_patient.php?id_paciente=<?php echo $fila['id_paciente']; ?>" class="btn-modificar">
                            <i class="fas fa-edit"> </i> Modificar 
                        </a>
                        <a href="delete_patient.php?id_paciente=<?php echo $fila['id_paciente']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este paciente?');">
                            <i class="fas fa-trash"> </i> Eliminar 
                        </a>
                        <a href="citas.php?id_paciente=<?php echo $fila['id_paciente']; ?>">
                            <i class="fas fa-calendar-alt"> </i> Citas 
                        </a>
                        <a href="historial_medico.php?id_paciente=<?php echo $fila['id_paciente'];?>">
                            <i class="fas fa-file-medical-alt"> </i> Historial Médico 
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <p style="margin-top: 20px;">No se encontraron resultados.</p>
<?php endif; ?>

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
