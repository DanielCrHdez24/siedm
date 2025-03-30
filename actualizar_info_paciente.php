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
    // Verificar si el ID del paciente existe
    if (!isset($_POST['id_paciente']) || empty($_POST['id_paciente'])) {
        die("Error: ID de paciente no proporcionado.");
    }

    $id_paciente = (int) $_POST['id_paciente'];  // Asegurar que es un número entero

    // Lista de campos requeridos
    $required_fields = ['clave_expediente', 'curp', 'edad', 'sexo', 'fecha_nacimiento', 'derechohabiencia', 'direccion', 'tipo_sangre', 'ocupacion'];

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

    // Preparar la consulta de actualización (UPDATE)
    if ($fotoPath) {
        $sql = "UPDATE pacientes 
                SET foto = ?, curp = ?, edad = ?, sexo = ?, fecha_nacimiento = ?, 
                    derechohabiencia = ?, direccion = ?, tipo_sangre = ?, religion = ?, ocupacion = ?, 
                    alergias = ?, padecimientos = ?, fecha_actualizacion = NOW() 
                WHERE id_paciente = ?";
    } else {
        $sql = "UPDATE pacientes 
                SET curp = ?, edad = ?, sexo = ?, fecha_nacimiento = ?, 
                    derechohabiencia = ?, direccion = ?, tipo_sangre = ?, religion = ?, ocupacion = ?, 
                    alergias = ?, padecimientos = ?, fecha_actualizacion = NOW() 
                WHERE id_paciente = ?";
    }

    if ($stmt = $link->prepare($sql)) {
        if ($fotoPath) {
            $stmt->bind_param("ssisssssssssi", 
                $fotoPath, $curp, $edad, $sexo, $fecha_nacimiento, 
                $derechohabiencia, $direccion, $tipo_sangre, $religion, $ocupacion, 
                $alergias, $padecimientos, $id_paciente
            );
        } else {
            $stmt->bind_param("sisssssssssi", 
                $curp, $edad, $sexo, $fecha_nacimiento, 
                $derechohabiencia, $direccion, $tipo_sangre, $religion, $ocupacion, 
                $alergias, $padecimientos, $id_paciente
            );
        }

        if ($stmt->execute()) {
            $stmt->close();
            $link->close();

            // Redirigir con un mensaje de éxito
            header('Location: paciente.php?id_paciente=' . $id_paciente . '&mensaje=Información+de+paciente+actualizada+correctamente!');
            exit();
        } else {
            die("Error al actualizar el expediente: " . $stmt->error);
        }
    } else {
        die("Error en la preparación de la consulta: " . $link->error);
    }
}

// Cerrar la conexión
$link->close();
?>
