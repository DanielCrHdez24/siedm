<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

// Incluir la conexión a la base de datos
include 'conexion.php'; // Asegúrate de tener este archivo con tus credenciales de base de datos

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recoger los datos del formulario
    $fotoPaciente = $_FILES['fotoPaciente']['name'];
    $claveExpediente = $_POST['claveExpediente'];
    $nombrePaciente = $_POST['nombrePaciente'];
    $primerApellido = $_POST['primerApellido'];
    $segundoApellido = $_POST['segundoApellido'];
    $curp = $_POST['curp'];
    $edad = $_POST['edad'];
    $sexo = $_POST['sexo'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $derechohabiencia = $_POST['derechohabiencia'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $tipoSangre = $_POST['tipoSangre'];
    $religion = $_POST['religion'];
    $ocupacion = $_POST['ocupacion'];
    $alergias = $_POST['alergias'];
    $padecimientos = $_POST['padecimientos'];

    // Validar que los campos no estén vacíos
    if (empty($claveExpediente) || empty($nombrePaciente) || empty($primerApellido) || empty($segundoApellido)) {
        echo "Por favor, complete todos los campos obligatorios.";
        exit();
    }

    // Guardar la foto si se sube
    if ($_FILES['fotoPaciente']['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['fotoPaciente']['tmp_name'];
        $fotoPath = 'uploads/' . basename($fotoPaciente); // Guardar la foto en una carpeta 'uploads'
        move_uploaded_file($tmp_name, $fotoPath);
    } else {
        $fotoPath = null; // Si no se sube foto, guardamos un valor nulo
    }

    // Insertar datos en la base de datos
    $sql = "INSERT INTO pacientes (clave_expediente, foto, nombre, primer_apellido, segundo_apellido, curp, edad, sexo, fecha_nacimiento, derechohabiencia, telefono, direccion, tipo_sangre, religion, ocupacion, alergias, padecimientos, fecha_registro) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    if ($stmt = $link->prepare($sql)) {
        // Enlazar los parámetros
        // Asegúrate de que la cadena de tipo tenga un 's' para cada valor de cadena
        $stmt->bind_param("ssssssssssssssssss", 
                          $claveExpediente, 
                          $fotoPath, 
                          $nombrePaciente, 
                          $primerApellido, 
                          $segundoApellido, 
                          $curp, 
                          $edad, 
                          $sexo, 
                          $fechaNacimiento, 
                          $derechohabiencia, 
                          $telefono, 
                          $direccion, 
                          $tipoSangre, 
                          $religion, 
                          $ocupacion, 
                          $alergias, 
                          $padecimientos);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Expediente agregado con éxito.";
            // Puedes redirigir a otra página o mostrar un mensaje de éxito
            // header("location: success.php"); 
        } else {
            echo "Error al agregar el expediente: " . $stmt->error;
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $link->error;
    }

    // Cerrar la conexión
    $link->close();
}
?>
