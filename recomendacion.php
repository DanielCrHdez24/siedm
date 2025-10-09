<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

include "conexion.php";

$ok_msg = $err_msg = null;

// ==================== Guardar la recomendación ====================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $doctor_id   = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;
    $trato       = isset($_POST['trato']) ? (int)$_POST['trato'] : 0;
    $puntualidad = isset($_POST['puntualidad']) ? (int)$_POST['puntualidad'] : 0;
    $comunicacion= isset($_POST['comunicacion']) ? (int)$_POST['comunicacion'] : 0;
    $comentario  = trim($_POST['comentario']);

    if ($doctor_id > 0 && $trato && $puntualidad && $comunicacion) {
        $promedio = round(($trato + $puntualidad + $comunicacion) / 3);

        $sql = "INSERT INTO recomendaciones 
                (paciente_id, persona_id, tipo_servicio, calificacion, comentario) 
                VALUES (?, ?, 'medico', ?, ?)";
        if ($stmt = $link->prepare($sql)) {
            $stmt->bind_param("iiis", $_SESSION['id_usuario'], $doctor_id, $promedio, $comentario);
            if ($stmt->execute()) {
                $ok_msg = "✅ ¡Gracias por tu evaluación!";
            } else {
                $err_msg = "Error al guardar: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $err_msg = "Por favor completa todas las preguntas antes de enviar.";
    }
}

// ==================== Obtener lista de médicos ====================
$medicos = [];
$idRolMedico = 2; // <-- Filtra por médicos

$res = $link->query("SELECT id_usuario, 
                            CONCAT(nombre,' ',primer_apellido,' ',segundo_apellido) AS nombre,
                            cedula_profesional
    FROM usuarios
    WHERE id_rol = $idRolMedico
    ORDER BY nombre ASC");

while ($row = $res->fetch_assoc()) {
    $medicos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación de Doctores</title>
    <link rel="stylesheet" href="css/styles_desktop.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .card {
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .rating-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .rating-group label {
            display: inline-block;
            text-align: center;
            cursor: pointer;
            font-weight: bold;
            width: 30px;
            background: #f1f1f1;
            border-radius: 5px;
            transition: background 0.2s;
        }
        .rating-group input[type="radio"] {
            display: none;
        }
        .rating-group input[type="radio"]:checked + label {
            background: #009688;
            color: white;
        }
    </style>
</head>
<body class="principal">
<div class="wrapper">
<header class="header">
    <a href="#" class="logo"><img src="./images/logo.png" width="150px"></a>
    <nav class="navbar">
        <a href="panel.php">Dashboard</a>
        <a href="citas.php">Gestión de Citas</a>
        <a href="recomendacion.php" class="active">Recomendaciones</a>
        <a href="logout.php" class="logout-link">Cerrar sesión</a>
        <span style="font-size: 0.7em;">Usuario: <?php echo $_SESSION["nombreUsuario"]; ?></span>
    </nav>
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
</header>

<div class="container">
    <h2>Encuesta de Evaluación de médico</h2>
    <p>Tu opinión es muy importante para mejorar la atención médica.</p>

    <?php if ($ok_msg): ?><div class="alert-success"><?= $ok_msg ?></div><?php endif; ?>
    <?php if ($err_msg): ?><div class="alert-error"><?= $err_msg ?></div><?php endif; ?>

    <div class="card">
        <form method="post">
            <label>Selecciona al medico:</label>
            <select name="doctor_id" required>
                <option value="">— Selecciona —</option>
                <?php foreach ($medicos as $m): 
                    $nombre = htmlspecialchars($m['nombre']);
                    $cedula = $m['cedula_profesional'] ? " - Cédula: ".htmlspecialchars($m['cedula_profesional']) : "";
                ?>
                    <option value="<?= $m['id_usuario'] ?>"><?= $nombre ?><?= $cedula ?></option>
                <?php endforeach; ?>
            </select>

            <label>Trato al paciente:</label>
            <div class="rating-group">
                <?php for ($i=1; $i<=5; $i++): ?>
                    <input type="radio" id="trato<?= $i ?>" name="trato" value="<?= $i ?>">
                    <label for="trato<?= $i ?>"><?= $i ?></label>
                <?php endfor; ?>
            </div>

            <label>Puntualidad:</label>
            <div class="rating-group">
                <?php for ($i=1; $i<=5; $i++): ?>
                    <input type="radio" id="puntualidad<?= $i ?>" name="puntualidad" value="<?= $i ?>">
                    <label for="puntualidad<?= $i ?>"><?= $i ?></label>
                <?php endfor; ?>
            </div>

            <label>Claridad en la comunicación:</label>
            <div class="rating-group">
                <?php for ($i=1; $i<=5; $i++): ?>
                    <input type="radio" id="comunicacion<?= $i ?>" name="comunicacion" value="<?= $i ?>">
                    <label for="comunicacion<?= $i ?>"><?= $i ?></label>
                <?php endfor; ?>
            </div>

            <label>Comentarios adicionales:</label>
            <textarea name="comentario" rows="3" placeholder="Escribe tus observaciones (opcional)..."></textarea>

            <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Enviar</button>
        </form>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2025 - SIEDM</p>
</footer>
</div>

<script>
function toggleMenu() {
    const navbar = document.querySelector('.navbar');
    navbar.classList.toggle('responsive');
}
</script>
</body>
</html>
