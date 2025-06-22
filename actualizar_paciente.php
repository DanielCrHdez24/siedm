<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_paciente = $_POST['id_paciente'];
    $nombre = trim($_POST['nombre']);
    $primer_apellido = trim($_POST['primer_apellido']);
    $segundo_apellido = trim($_POST['segundo_apellido']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $curp = trim($_POST['curp']);
    $edad = $_POST['edad'];
    $sexo = $_POST['sexo'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $derechohabiencia = $_POST['derechohabiencia'];
    $direccion = trim($_POST['direccion']);
    $tipo_sangre = $_POST['tipo_sangre'];
    $religion = trim($_POST['religion']);
    $ocupacion = trim($_POST['ocupacion']);
    $alergias = trim($_POST['alergias']);
    $padecimientos = trim($_POST['padecimientos']);
    $fecha_actualizacion = date("Y-m-d H:i:s");


    // Validar correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo "Correo electrónico inválido.";
        exit();
    }

    // Procesar imagen si se envió una nueva
    $foto_ruta = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $nombre_foto = basename($_FILES['foto']['name']);
        $ruta_destino = "uploads/" . time() . "_" . $nombre_foto;

        if (move_uploaded_file($foto_tmp, $ruta_destino)) {
            $foto_ruta = $ruta_destino;
        } else {
            echo "Error al subir la foto.";
            exit();
        }
    }

    // Construir consulta SQL de actualización
    $sql = "UPDATE pacientes SET 
                nombre = ?, 
                primer_apellido = ?, 
                segundo_apellido = ?, 
                correo = ?, 
                telefono = ?, 
                curp = ?, 
                edad = ?, 
                sexo = ?, 
                fecha_nacimiento = ?, 
                derechohabiencia = ?, 
                direccion = ?, 
                tipo_sangre = ?, 
                religion = ?, 
                ocupacion = ?, 
                alergias = ?, 
                padecimientos = ?,
                fecha_actualizacion = ?";

    // Si se subió una nueva foto, actualizarla también
    if ($foto_ruta !== null) {
        $sql .= ", foto = ?";
    }

    $sql .= " WHERE id_paciente = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        if ($foto_ruta !== null) {
    mysqli_stmt_bind_param($stmt, "sssssssissssssssssi",
        $foto_ruta, $nombre, $primer_apellido, $segundo_apellido, $correo, $telefono,
        $curp, $edad, $sexo, $fecha_nacimiento, $derechohabiencia,
        $direccion, $tipo_sangre, $religion, $ocupacion,
        $alergias, $padecimientos,$fecha_actualizacion, $id_paciente
    );
} else {
    mysqli_stmt_bind_param($stmt, "ssssssissssssssssi",
        $nombre, $primer_apellido, $segundo_apellido, $correo, $telefono,
        $curp, $edad, $sexo, $fecha_nacimiento, $derechohabiencia,
        $direccion, $tipo_sangre, $religion, $ocupacion,
        $alergias, $padecimientos, $fecha_actualizacion, $id_paciente
    );
}

        if (mysqli_stmt_execute($stmt)) {
            header('Location: paciente.php?id_paciente=' . $id_paciente . '&mensaje=Información+de+paciente+actualizada+correctamente!');
            exit();
        } else {
            echo "Error al actualizar paciente: " . mysqli_error($link);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error en la preparación de la consulta: " . mysqli_error($link);
    }
} else {
    echo "Acceso no autorizado.";
}

mysqli_close($link);
?>
