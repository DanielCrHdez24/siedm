<?php
require_once "conexion.php";

if (isset($_GET['id_paciente'])) {
    $id_paciente = intval($_GET['id_paciente']);
    $query = "SELECT foto FROM pacientes WHERE id_paciente = $id_paciente";
    $result = mysqli_query($link, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(["foto" => $row['foto']]);
    } else {
        echo json_encode(["foto" => null]);
    }
}
?>
