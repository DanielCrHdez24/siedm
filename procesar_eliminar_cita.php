<?php
session_start();
require_once "conexion.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_paciente = intval($_POST["id_paciente"]);
    $id_usuario = $_SESSION["loggedin"];
    $id_cita = $_POST["id_cita"];
    $estado = "CANCELADA";

    // Verifica que el id_usuario esté definido
    if (!$id_usuario) {
        die("Error: No se encontró el ID del usuario.");
    }

    $sql = "DELETE FROM citas_medicas WHERE id_cita = ?";

    if ($stmt = $link->prepare($sql)) {
        $stmt->bind_param("i", $id_cita);

        if ($stmt->execute()) {
            $stmt->close();
            $link->close();

            header('Location: cancelar_cita.php?&mensaje=Cita+de+paciente+cancelada+correctamente!');
            exit();
        } else {
            die("Error al cancelar la cita: " . $stmt->error);
        }
    } else {
        die("Error en la preparación de la consulta: " . $link->error);
    }
}

$link->close();

?>

