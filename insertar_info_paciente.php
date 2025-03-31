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
    $required_fields = ['clave_expediente', 'curp', 'edad', 'sexo', 'fecha_nacimiento', 'derechohabiencia', 'direccion', 'tipo_sangre', 'ocupacion', 'id_usuario'];

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

    // Recoger datos del formulario y sanitizar
    $clave_expediente  = trim($_POST['clave_expediente']);
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
    $id_usuario       = (int) $_POST['id_usuario'];

    // Preparar la consulta SQL
    $sql = "INSERT INTO pacientes (clave_expediente, foto, curp, edad, sexo, fecha_nacimiento, derechohabiencia, direccion, tipo_sangre, religion, ocupacion, alergias, padecimientos, fecha_registro, id_usuario) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param("sssisssssssssi", 
            $clave_expediente, $fotoPath, $curp, $edad, $sexo, $fecha_nacimiento, 
            $derechohabiencia, $direccion, $tipo_sangre, $religion, $ocupacion, 
            $alergias, $padecimientos, $id_usuario
        );

        if ($stmt->execute()) {
            $id_paciente = $stmt->insert_id; // ID del nuevo paciente
            $stmt->close();
            $link->close();

            // Redirigir correctamente a paciente.php
            header('Location: paciente.php?id_paciente=' . $id_paciente . '&mensaje=Información+de+paciente+agregada+correctamente!');
            exit();
        } else {
            die("Error al agregar el expediente: " . $stmt->error);
        }
    } else {
        die("Error en la preparación de la consulta: " . $link->error);
    }
}

// Cerrar la conexión
$link->close();
?>
