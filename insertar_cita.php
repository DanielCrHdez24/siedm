<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_paciente = intval($_POST["id_paciente"]);
    $fecha_cita = $_POST["fecha_cita"];
    $motivo = mysqli_real_escape_string($link, $_POST["motivo"]);
    $estado = "Pendiente";

    // Obtener clave de expediente del paciente
    $query_expediente = "SELECT clave_expediente FROM pacientes WHERE id_paciente = $id_paciente";
    $result_expediente = mysqli_query($link, $query_expediente);
    $row_expediente = mysqli_fetch_assoc($result_expediente);
    $clave_expediente = $row_expediente["clave_expediente"];

    // Insertar la cita en la tabla citas_medicas
    $query = "INSERT INTO citas_medicas (id_expediente, fecha_cita, motivo, estado) 
            VALUES ('$clave_expediente', '$fecha_cita', '$motivo', '$estado')";

    if (mysqli_query($link, $query)) {
        header("Location: citas.php?mensaje=Cita agendada con éxito");
        exit();
    } else {
        die("Error al insertar la cita: " . mysqli_error($link));
    }
}
