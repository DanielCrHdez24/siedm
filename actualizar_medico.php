<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
if ($idRol != 1) {
    header("location: panel.php");
    exit();
}
// Conexión a la base de datos
require_once 'conexion.php';

// Verifica si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST['id_usuario'];
    $nombre = trim($_POST['nombre']);
    $primer_apellido = trim($_POST['primer_apellido']);
    $segundo_apellido = trim($_POST['segundo_apellido']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $contrasena = $_POST['contrasena'];
    $contrasena2 = $_POST['contrasena2'];

    // Validación: Verificar que las contraseñas coincidan
    if (!empty($contrasena) && $contrasena !== $contrasena2) {
        header("location: update_medical.php?id_usuario=$id_usuario&error=Las+contraseñas+no+coinciden");
        exit();
    }

    // Validación del correo electrónico
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("location: update_medical.php?id_usuario=$id_usuario&error=Correo+electrónico+inválido");
        exit();
    }

    // Consultar el ID del usuario asociado al paciente
    $sql_usuario = "SELECT id_usuario FROM usuarios WHERE id_usuario = ? AND id_rol IN (2,3)";
    if ($stmt_usuario = mysqli_prepare($link, $sql_usuario)) {
        mysqli_stmt_bind_param($stmt_usuario, "i", $id_usuario);
        mysqli_stmt_execute($stmt_usuario);
        mysqli_stmt_bind_result($stmt_usuario, $id_encontrado);

        if (!mysqli_stmt_fetch($stmt_usuario)) {
            header("location: update_medical.php?id_usuario=$id_usuario&error=ID+inválido");
            exit();
        }
        mysqli_stmt_close($stmt_usuario);
    } else {
        header("location: update_medical.php?id_usuario=$id_usuario&error=Error+al+consultar+usuario");
        exit();
    }

    // Si hay una nueva contraseña, la encriptamos
    $contrasena_hash = !empty($contrasena) ? password_hash($contrasena, PASSWORD_BCRYPT) : null;

    // Preparar la consulta de actualización
    if ($contrasena_hash) {
        $sql = "UPDATE usuarios 
                SET nombre = ?, primer_apellido = ?, segundo_apellido = ?, correo = ?, telefono = ?, contrasena = ? 
                WHERE id_usuario = ?";
    } else {
        $sql = "UPDATE usuarios 
                SET nombre = ?, primer_apellido = ?, segundo_apellido = ?, correo = ?, telefono = ? 
                WHERE id_usuario = ?";
    }

    if ($stmt = mysqli_prepare($link, $sql)) {
        if ($contrasena_hash) {
            mysqli_stmt_bind_param($stmt, "ssssssi", $nombre, $primer_apellido, $segundo_apellido, $correo, $telefono, $contrasena_hash, $id_usuario);
        } else {
            mysqli_stmt_bind_param($stmt, "sssssi", $nombre, $primer_apellido, $segundo_apellido, $correo, $telefono, $id_usuario);
        }

        if (mysqli_stmt_execute($stmt)) {
            if ($_SESSION['idRol'] == 1) {
               
                header('Location: medico.php?id_usuario=' . $id_usuario . '&mensaje=Información+de+usuario+actualizada+correctamente!');
            } else {
                // Redirigir a paciente.php para otros roles
                header('Location: panel.php?id_usuario=' . $id_usuario . '&mensaje=Mi+información+fue+actualizada+correctamente!');
            }
            exit();
        } else {
            echo "Error al actualizar usuario: " . mysqli_error($link);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error en la consulta de actualización: " . mysqli_error($link);
    }
} else {
    echo "Acceso no autorizado.";
}

mysqli_close($link);
