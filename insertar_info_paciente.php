<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];

// Incluir la conexión a la base de datos
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lista de campos requeridos
    $required_fields = ['nombre', 'primer_apellido', 'segundo_apellido', 'correo', 'telefono', 'curp', 'edad', 'sexo', 'fecha_nacimiento', 'derechohabiencia', 'direccion', 'tipo_sangre', 'ocupacion'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            die("Error: El campo '$field' es obligatorio.");
        }
    }

    // Procesar la imagen (si existe)
    $fotoPath = null;
    if (!empty($_FILES['foto']['name'])) {
        $tmp_name = $_FILES['foto']['tmp_name'];
        $file_name = basename($_FILES['foto']['name']);
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_type, $allowed_types)) {
            $fotoPath = 'uploads/' . uniqid() . "_" . $file_name;
            if (!move_uploaded_file($tmp_name, $fotoPath)) {
                die("Error al subir la imagen.");
            }
        } else {
            die("Error: Solo se permiten imágenes JPG, PNG o GIF.");
        }
    }

    // Recoger datos del formulario
    $nombre           = trim($_POST['nombre']);
    $primer_apellido  = trim($_POST['primer_apellido']);
    $segundo_apellido = trim($_POST['segundo_apellido']);
    $correo           = trim($_POST['correo']);
    $telefono         = trim($_POST['telefono']);
    $curp             = trim($_POST['curp']);
    $edad             = (int) $_POST['edad'];
    $sexo             = trim($_POST['sexo']);
    $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
    $derechohabiencia = trim($_POST['derechohabiencia']);
    $direccion        = trim($_POST['direccion']);
    $tipo_sangre      = trim($_POST['tipo_sangre']);
    $religion         = trim($_POST['religion'] ?? '');
    $ocupacion        = trim($_POST['ocupacion']);
    $alergias         = trim($_POST['alergias'] ?? '');
    $padecimientos    = trim($_POST['padecimientos'] ?? '');

    // Verificar si la CURP ya existe
    $sql_check = "SELECT curp FROM pacientes WHERE curp = ?";
    $stmt_check = $link->prepare($sql_check);
    $stmt_check->bind_param("s", $curp);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        $link->close();
        echo "<script>
            alert('⚠️ La CURP - $curp ya está registrada.');
            history.back();
          </script>";
        exit();
    }
    $stmt_check->close();

    // Insertar paciente
    $sql = "INSERT INTO pacientes (foto, nombre, primer_apellido, segundo_apellido, correo, telefono, curp, edad, sexo, fecha_nacimiento, derechohabiencia, direccion, tipo_sangre, religion, ocupacion, alergias, padecimientos, fecha_registro) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param(
            "sssssssisssssssss",
            $fotoPath,
            $nombre,
            $primer_apellido,
            $segundo_apellido,
            $correo,
            $telefono,
            $curp,
            $edad,
            $sexo,
            $fecha_nacimiento,
            $derechohabiencia,
            $direccion,
            $tipo_sangre,
            $religion,
            $ocupacion,
            $alergias,
            $padecimientos
        );

        if ($stmt->execute()) {
            $id_paciente = $stmt->insert_id;
            $stmt->close();

            // 🔹 Crear automáticamente un historial médico vacío vinculado al paciente
            $sql_historial = "INSERT INTO historial_medico (id_paciente, fecha_creacion) VALUES (?, NOW())";
            $stmt_historial = $link->prepare($sql_historial);
            $stmt_historial->bind_param("i", $id_paciente);
            $stmt_historial->execute();
            $stmt_historial->close();

            $link->close();

            // Redirigir correctamente
            header('Location: paciente.php?id_paciente=' . $id_paciente . '&mensaje=Paciente+e+historial+creados+correctamente');
            exit();
        } else {
            die("Error al agregar el expediente: " . $stmt->error);
        }
    } else {
        die("Error en la preparación de la consulta: " . $link->error);
    }
}

// Cerrar conexión
$link->close();
?>
