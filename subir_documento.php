<?php
session_start();
require_once "conexion.php";

// Verifica que haya sesión activa
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["archivo"])) {
    $id_paciente = intval($_POST["id_paciente"]);
    $tipo_documento = $_POST["tipo_documento"];
    $archivo = $_FILES["archivo"];
    $nombre_original = basename($archivo["name"]);
    $ruta_destino = "documents/" . uniqid() . "_" . $nombre_original;
    $fecha_subida = date("Y-m-d H:i:s");

    $extensiones_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

    // Validar extensión
    if (!in_array($extension, $extensiones_permitidas)) {
        die("Error: Extensión de archivo no permitida.");
    }

    // Mover archivo al servidor
    if (move_uploaded_file($archivo["tmp_name"], $ruta_destino)) {
        // Guardar en la base de datos
        $sql = "INSERT INTO documentos_digitalizados (id_paciente, tipo_documento, nombre_archivo, ruta_archivo, fecha_subida)
                VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $link->prepare($sql)) {
            $stmt->bind_param("issss", $id_paciente, $tipo_documento, $nombre_original, $ruta_destino, $fecha_subida);
            if ($stmt->execute()) {
                $stmt->close();
                $link->close();
                header("Location: historial_medico.php?id_paciente=" . $id_paciente . "&mensaje=Documento+subido+con+éxito");
                exit();
            } else {
                die("Error al insertar en la base de datos: " . $stmt->error);
            }
        } else {
            die("Error al preparar la consulta: " . $link->error);
        }
    } else {
        die("Error al mover el archivo.");
    }
} else {
    die("Solicitud inválida.");
}
?>
