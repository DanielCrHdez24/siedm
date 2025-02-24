<?php
$host = 'localhost';
$dbname = 'siedm';
$username = 'root';
$password = '';

// Crear conexión
$conn = mysqli_connect($host, $username, $password, $dbname);

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

$usuario = 'Daniel Cruz Hernández';
$contrasena = '123456';
$contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
$rol = '2';
$cedula = '19191919';
$fecha = '2025-02-23 00:00:00';

// Preparar la consulta SQL
$sql = 'INSERT INTO usuarios (nombreUsuario, contrasena, rol, cedulaProf, fechaReg) VALUES (?, ?, ?, ?, ?)';

if ($stmt = mysqli_prepare($conn, $sql)) {
    // Vincular parámetros
    mysqli_stmt_bind_param($stmt, 'ssiss', $usuario, $contrasena_hash, $rol, $cedula, $fecha);
    
    // Ejecutar la consulta
    if (mysqli_stmt_execute($stmt)) {
        echo "Usuario insertado correctamente.";
    } else {
        echo "Error al insertar usuario: " . mysqli_error($conn);
    }

    // Cerrar la declaración
    mysqli_stmt_close($stmt);
} else {
    echo "Error al preparar la consulta: " . mysqli_error($conn);
}

// Cerrar la conexión
mysqli_close($conn);
?>
