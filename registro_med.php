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

// Datos del médico
$nombre = 'Ricardo';
$primer_apellido = 'Mendoza';
$segundo_apellido = 'López';
$correo = 'ricardo.mendoza@example.com';
$telefono = '5551234567';
$contrasena = 'medico123';
$contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);
$rol_id = 2; // ID del rol "Médico"
$cedula_profesional = '987654321012';

// Insertar usuario en la tabla
$sql = 'INSERT INTO usuarios (nombre, primer_apellido, segundo_apellido, correo, telefono, contrasena, rol_id, cedula_profesional) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'ssssssii', $nombre, $primer_apellido, $segundo_apellido, $correo, $telefono, $contrasena_hash, $rol_id, $cedula_profesional);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Médico insertado correctamente.";
    } else {
        echo "Error al insertar médico: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error al preparar la consulta: " . mysqli_error($conn);
}

// Cerrar conexión
mysqli_close($conn);
?>
