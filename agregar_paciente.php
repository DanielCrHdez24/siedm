<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

// Conexión a la base de datos
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $primer_apellido = trim($_POST['primer_apellido']);
    $segundo_apellido = trim($_POST['segundo_apellido']);
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    $contrasena2 = $_POST['contrasena2'];
    $id_rol = $_POST['id_rol']; // hidden input en el form

    // Validación básica
    if ($contrasena !== $contrasena2) {
        echo "Las contraseñas no coinciden.";
        exit();
    }

    // Validación adicional para el correo (opcional)
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo "Correo electrónico inválido.";
        exit();
    }

    $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
    $fecha_registro = date('Y-m-d H:i:s');

    // Preparar la consulta SQL
    $sql = 'INSERT INTO usuarios (nombre, primer_apellido, segundo_apellido, correo, contrasena, id_rol, fecha_registro) 
            VALUES (?, ?, ?, ?, ?, ?, ?)';

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 'sssssis', $nombre, $primer_apellido, $segundo_apellido, $correo, $contrasena_hash, $id_rol, $fecha_registro);

        if (mysqli_stmt_execute($stmt)) {
            // Redirección limpia después del insert
            header('Location: panel.php?mensaje=Paciente+agregado+correctamente');
            exit();
        } else {
            echo "Error al agregar paciente: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error al preparar la consulta: " . mysqli_error($conn);
    }
} else {
    echo "Acceso no autorizado.";
}

mysqli_close($conn);
?>
