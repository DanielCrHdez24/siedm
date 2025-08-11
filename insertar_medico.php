<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];

// Conexión a la base de datos
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST['nombre']);
    $primer_apellido = trim($_POST['primer_apellido']);
    $segundo_apellido = trim($_POST['segundo_apellido']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $contrasena = $_POST['contrasena'];
    $contrasena2 = $_POST['contrasena2'];
    $id_rol = $_POST['id_rol']; // hidden input en el form
    $estado = 'ACTIVO'; // Estado por defecto
    $especialidad = trim($_POST['especialidad']);
    $cedula_profesional = trim($_POST['cedula_profesional']);

    // Validaciones básicas
    if ($contrasena !== $contrasena2) {
        header('Location: add_medical.php?error=Las+contraseñas+no+coinciden');
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header('Location: add_medical.php?error=Correo+electrónico+inválido');
        exit();
    }

    // Verificar si el correo ya existe
    $sql_check = "SELECT id_usuario FROM usuarios WHERE correo = ?";
    if ($stmt_check = mysqli_prepare($link, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "s", $correo);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            // Ya existe el correo
            mysqli_stmt_close($stmt_check);
            header('Location: add_medical.php?error=El+correo+ya+está+registrado,+llene+el+formulario+de+nuevo.');
            exit();
        }
        mysqli_stmt_close($stmt_check);
    }

    // Hashear contraseña
    $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
    $fecha_registro = date('Y-m-d H:i:s');

    // Preparar INSERT
    $sql = 'INSERT INTO usuarios 
        (nombre, primer_apellido, segundo_apellido, correo, telefono, contrasena, id_rol, fecha_registro, cedula_profesional, especialidad, estado) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 'ssssssissss', 
            $nombre, 
            $primer_apellido, 
            $segundo_apellido, 
            $correo, 
            $telefono, 
            $contrasena_hash, 
            $id_rol, 
            $fecha_registro, 
            $cedula_profesional, 
            $especialidad, 
            $estado
        );

        if (mysqli_stmt_execute($stmt)) {
            $id_usuario = mysqli_insert_id($link);
            mysqli_stmt_close($stmt);
            header('Location: medico.php?id_usuario=' . $id_usuario . '&mensaje=Médico+o+Recepcionista+agregado+correctamente');
            exit();
        } else {
            if (mysqli_errno($link) == 1062) {
                // Duplicado por carrera de inserciones
                header('Location: add_medical.php?error=El+correo+ya+está+registrado,+llene+el+formulario+de+nuevo.');
            } else {
                header('Location: add_medical.php?error=Error+al+agregar+el+usuario');
            }
            mysqli_stmt_close($stmt);
            exit();
        }

    } else {
        header('Location: add_medical.php?error=Error+al+preparar+la+consulta');
        exit();
    }
} else {
    header('Location: add_medical.php?error=Acceso+no+autorizado');
    exit();
}

mysqli_close($link);
?>
