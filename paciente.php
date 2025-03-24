<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

include 'conexion.php';

// Recibir el id_paciente de la URL
$id_paciente = $_GET['id_paciente'] ?? null;

if (!$id_paciente) {
    die("ID de paciente no proporcionado.");
}

// Consulta de datos del paciente
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
    die("Error en la consulta: " . $link->error);
}

$link->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Paciente</title>
    <link rel="stylesheet" href="css/styles_desktop.css">
</head>
<body>

    <h1>Detalles del Paciente</h1>

    <?php if (isset($_GET['mensaje'])): ?>
        <p style="color:green;"><?php echo htmlspecialchars($_GET['mensaje']); ?></p>
    <?php endif; ?>

    <div class="paciente-detalles">
        <img src="<?php echo htmlspecialchars($paciente['foto']); ?>" alt="Foto del paciente" width="150">
        <p><strong>Clave de Expediente:</strong> <?php echo htmlspecialchars($paciente['clave_expediente']); ?></p>
        <p><strong>CURP:</strong> <?php echo htmlspecialchars($paciente['curp']); ?></p>
        <p><strong>Edad:</strong> <?php echo htmlspecialchars($paciente['edad']); ?></p>
        <p><strong>Sexo:</strong> <?php echo htmlspecialchars($paciente['sexo']); ?></p>
        <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($paciente['fecha_nacimiento']); ?></p>
        <p><strong>Derechohabiencia:</strong> <?php echo htmlspecialchars($paciente['derechohabiencia']); ?></p>
        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($paciente['direccion']); ?></p>
        <p><strong>Tipo de Sangre:</strong> <?php echo htmlspecialchars($paciente['tipo_sangre']); ?></p>
        <p><strong>Religión:</strong> <?php echo htmlspecialchars($paciente['religion']); ?></p>
        <p><strong>Ocupación:</strong> <?php echo htmlspecialchars($paciente['ocupacion']); ?></p>
        <p><strong>Alergias:</strong> <?php echo htmlspecialchars($paciente['alergias']); ?></p>
        <p><strong>Padecimientos Crónicos:</strong> <?php echo htmlspecialchars($paciente['padecimientos']); ?></p>
        <p><strong>Fecha de Registro:</strong> <?php echo htmlspecialchars($paciente['fecha_registro']); ?></p>
    </div>

    <a href="panel.php" class="btn-back">Volver al panel</a>

</body>
</html>
