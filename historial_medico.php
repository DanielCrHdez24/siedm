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
$historial_result = null;
$docs_result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "SELECT * FROM pacientes WHERE nombre LIKE ? OR curp LIKE ? OR id_paciente = ?";
    $stmt = $link->prepare($sql);
    $like = "%" . $busqueda . "%";
    $stmt->bind_param("ssi", $like, $like, $busqueda);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $paciente = $resultado->fetch_assoc();

        // Obtener citas médicas
        $sql_citas = "SELECT * FROM citas_medicas WHERE id_paciente = ? ORDER BY fecha_cita DESC, hora_cita DESC";
        $stmt_citas = $link->prepare($sql_citas);
        $stmt_citas->bind_param("i", $paciente['id_paciente']);
        $stmt_citas->execute();
        $citas = $stmt_citas->get_result();

        // Obtener historial médico
        $sql_historial = "SELECT * FROM historial_medico WHERE id_paciente = ? ORDER BY fecha_consulta DESC";
        $stmt_historial = $link->prepare($sql_historial);
        $stmt_historial->bind_param("i", $paciente['id_paciente']);
        $stmt_historial->execute();
        $historial_result = $stmt_historial->get_result();

        // Obtener documentos digitalizados
        $sql_docs = "SELECT * FROM documentos_digitalizados WHERE id_expediente = ?";
        $stmt_docs = $link->prepare($sql_docs);
        $stmt_docs->bind_param("i", $paciente['id_paciente']); // Suponiendo que id_expediente = id_paciente
        $stmt_docs->execute();
        $docs_result = $stmt_docs->get_result();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial Médico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles_desktop.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="principal">
    <div class="wrapper">
        <header class="header">
            <a href="#" class="logo"><img src="./images/logo.png" alt="Logo SIEDM" width="150px" /></a>
            <nav class="navbar">
                <a href="panel.php">Dashboard</a>
                <a href="perfil.php">Mi perfil</a>
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
            <h2>Historial Médico del Paciente</h2>
            <form method="POST" class="form">
                <input type="text" name="busqueda" placeholder="Buscar por nombre, CURP o ID" required>
                <button type="submit">Buscar</button>
            </form>

            <?php if ($paciente): ?>
                <h3>Datos del Paciente</h3>
                <table class="table" style="font-size:80%;">
                    <tbody>
                        <tr>
                            <td rowspan="7" style="text-align: center;">
                                <img src="<?= htmlspecialchars($paciente['foto']) ?>" style="max-width:150px; max-height:150px;">
                            </td>
                        </tr>
                        <tr>
                            <th>Clave de Expediente</th>
                            <td><?= htmlspecialchars($paciente['clave_expediente']) ?></td>
                            <th>Nombre</th>
                            <td><?= htmlspecialchars($paciente['nombre']) . " " . htmlspecialchars($paciente['primer_apellido']) . " " . htmlspecialchars($paciente['segundo_apellido']) ?></td>
                            <th>CURP</th>
                            <td><?= htmlspecialchars($paciente['curp']) ?></td>
                        </tr>
                        <tr>
                            <th>Edad</th>
                            <td><?= htmlspecialchars($paciente['edad']) ?></td>
                            <th>Sexo</th>
                            <td><?= htmlspecialchars($paciente['sexo']) ?></td>
                            <th>Fecha Nac.</th>
                            <td><?= htmlspecialchars($paciente['fecha_nacimiento']) ?></td>
                        </tr>
                        <tr>
                            <th>Correo</th>
                            <td><?= htmlspecialchars($paciente['correo']) ?></td>
                            <th>Teléfono</th>
                            <td><?= htmlspecialchars($paciente['telefono']) ?></td>
                            <th>Derechohabiencia</th>
                            <td><?= htmlspecialchars($paciente['derechohabiencia']) ?></td>
                        </tr>
                        <tr>
                            <th>Dirección</th>
                            <td colspan="5"><?= htmlspecialchars($paciente['direccion']) ?></td>
                        </tr>
                        <tr>
                            <th>Tipo Sangre</th>
                            <td><?= htmlspecialchars($paciente['tipo_sangre']) ?></td>
                            <th>Religión</th>
                            <td><?= htmlspecialchars($paciente['religion']) ?></td>
                            <th>Ocupación</th>
                            <td><?= htmlspecialchars($paciente['ocupacion']) ?></td>
                        </tr>
                        <tr>
                            <th>Alergias</th>
                            <td><?= htmlspecialchars($paciente['alergias']) ?></td>
                            <th>Crónicos</th>
                            <td><?= htmlspecialchars($paciente['padecimientos']) ?></td>
                            <th>Fecha Registro</th>
                            <td><?= htmlspecialchars($paciente['fecha_registro']) ?></td>
                        </tr>
                    </tbody>
                </table>

                <h3>Citas Médicas</h3>
                <?php if ($citas->num_rows > 0): ?>
                    <table class="table" style="font-size:80%;">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Motivo</th>
                                <th>Diagnóstico</th>
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
                                    <td><?= htmlspecialchars($cita['estado']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay citas registradas.</p>
                <?php endif; ?>
                <br>
                <h3>Documentos Digitalizados</h3>
                <?php if ($docs_result && $docs_result->num_rows > 0): ?>
                    <ul>
                        <?php while ($doc = $docs_result->fetch_assoc()): ?>
                            <li>
                                <a href="<?= htmlspecialchars($doc['ruta_archivo']) ?>" target="_blank">
                                    <?= htmlspecialchars($doc['nombre_archivo']) ?> (<?= $doc['fecha_subida'] ?>)
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No hay documentos disponibles.</p>

                <?php endif; ?>
                <br>
                <h4>Subir nuevo documento:</h4>
                <form action="subir_documento.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id_expediente" value="<?= $paciente['id_paciente'] ?>">
                    <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required>
                    <button type="submit">Subir</button>
                </form>
                <br>
                <button onclick="window.print()" class="btn">🖨️ Imprimir / Guardar como PDF</button>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <p style="color:red;">No se encontró ningún paciente con esos datos.</p>
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