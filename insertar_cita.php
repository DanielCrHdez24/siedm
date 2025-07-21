<?php
session_start();
require_once "conexion.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_paciente = intval($_POST["id_paciente"]);
    $id_usuario = $_SESSION["id_usuario"]; // <-- Asegúrate que este valor esté en la sesión
    $fecha_cita = $_POST["fecha_cita"];
    $hora_cita = $_POST["hora_cita"];
    $motivo = $_POST["motivo"];
    $estado = "Pendiente";

    // Verifica que el id_usuario esté definido
    if (!$id_usuario) {
        die("Error: No se encontró el ID del usuario.");
    }

    $sql = "INSERT INTO citas_medicas (id_paciente, id_usuario, fecha_cita, hora_cita, motivo, estado) 
            VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param("iissss", 
            $id_paciente, $id_usuario, $fecha_cita, $hora_cita, $motivo, $estado);
            
        if ($stmt->execute()) {
            $stmt->close();
            $link->close();

            header('Location: agendar_cita.php?id_paciente=' . $id_paciente . '&mensaje=Cita+de+paciente+agregada+correctamente!');
            exit();
        } else {
            die("Error al agregar la cita: " . $stmt->error);
        }
    } else {
        die("Error en la preparación de la consulta: " . $link->error);
    }
}

$link->close();

?>

