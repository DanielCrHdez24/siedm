<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica si todos los campos POST existen
    $required_fields = ['clave_expediente', 'curp', 'edad', 'sexo', 'fecha_nacimiento', 'derechohabiencia', 'direccion', 'tipo_sangre', 'ocupacion', 'id_usuario'];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            die("Error: El campo '$field' es obligatorio.");
        }
    }

    $fotoPath = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['foto']['tmp_name'];
        $file_name = basename($_FILES['foto']['name']);
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_type, $allowed_types)) {
            $fotoPath = 'uploads/' . uniqid() . "_" . $file_name;
            move_uploaded_file($tmp_name, $fotoPath);
        } else {
            die("Error: Solo se permiten imágenes JPG, PNG o GIF.");
        }
    }

    // Recoger datos del formulario
    $clave_expediente = $_POST['clave_expediente'];
    $curp = $_POST['curp'];
    $edad = $_POST['edad'];
    $sexo = $_POST['sexo'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $derechohabiencia = $_POST['derechohabiencia'];
    $direccion = $_POST['direccion'];
    $tipo_sangre = $_POST['tipo_sangre'];
    $religion = $_POST['religion'] ?? '';
    $ocupacion = $_POST['ocupacion'];
    $alergias = $_POST['alergias'] ?? '';
    $padecimientos = $_POST['padecimientos'] ?? '';
    $id_usuario = $_POST['id_usuario'];

    // Preparamos el INSERT
    $sql = "INSERT INTO pacientes (clave_expediente, foto, curp, edad, sexo, fecha_nacimiento, derechohabiencia, direccion, tipo_sangre, religion, ocupacion, alergias, padecimientos, fecha_registro, id_usuario) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param("sssisssssssssi", 
            $clave_expediente,
            $fotoPath,
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
            $padecimientos,
            $id_usuario
        );

        if ($stmt->execute()) {
            echo "Expediente agregado con éxito.";
        } else {
            echo "Error al agregar el expediente: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error en la preparación: " . $link->error;
    }

    $link->close();
}
?>
