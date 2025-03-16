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

// Datos del usuario a insertar
$nombre = 'admin';
$primer_apellido = 'admin';
$segundo_apellido = 'admin';
$correo = 'admin@example.com';
$telefono = '1234567890';
$contrasena = '123456';
$contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
$rol_id = 1; // ID del rol de administrador
$paciente_id = NULL; // No es paciente, por lo tanto es NULL
$fecha_registro = date('Y-m-d H:i:s'); // Fecha de registro actual

// Preparar la consulta SQL
$sql = 'INSERT INTO usuarios (nombre, primer_apellido, segundo_apellido, correo, telefono, contrasena, rol_id, paciente_id, fecha_registro) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

if ($stmt = mysqli_prepare($conn, $sql)) {
    // Vincular parámetros
    mysqli_stmt_bind_param($stmt, 'ssssssiss', $nombre, $primer_apellido, $segundo_apellido, $correo, $telefono, $contrasena_hash, $rol_id, $paciente_id, $fecha_registro);
    
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

