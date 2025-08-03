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
    $estado = 'ACTIVO'; // Estado activo por defecto
    $cedula_profesional = trim($_POST['cedula']);

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
    $sql = 'INSERT INTO usuarios (nombre, primer_apellido, segundo_apellido, correo, telefono, contrasena, id_rol, fecha_registro, cedula_profesional, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 'ssssssis', $nombre, $primer_apellido, $segundo_apellido, $correo, $telefono, $contrasena_hash, $id_rol, $fecha_registro, $cedula_profesional, $estado);

        if (mysqli_stmt_execute($stmt)) {
            // Obtener el ID del usuario recién insertado
            $id_usuario = mysqli_insert_id($link); // Obtiene el ID generado automáticamente

            // Ahora puedes usar el ID del usuario
            echo "El ID del usuario recién insertado es: " . $id_usuario;

            // Redirigir o hacer lo que necesites con el ID
            header('Location: medico.php?id_usuario=' . $id_usuario . '&mensaje=Médico+o+Recepcionista+agregado+correctamente');
            exit();
        } else {
            echo "Error al agregar paciente: " . mysqli_error($link);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error al preparar la consulta: " . mysqli_error($link);
    }
} else {
    echo "Acceso no autorizado.";
}

mysqli_close($link);
?>