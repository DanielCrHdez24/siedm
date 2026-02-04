<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}
$idRol = $_SESSION['idRol'];
// Conexión a la base de datos
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $presion_arterial = trim($_POST['presion_arterial']);
    $frecuencia_cardiaca = trim($_POST['frecuencia_cardiaca']);
    $frecuencia_respiratoria = trim($_POST['frecuencia_respiratoria']);
    $saturacion_oxigeno = trim($_POST['saturacion_oxigeno']);
    $peso = trim($_POST['peso']);
    $talla = trim($_POST['talla']);
    $imc = trim($_POST['imc']);
    $temperatura = trim($_POST['temperatura']);
    $diagnostico = trim($_POST['diagnostico']);
    $indicaciones = trim($_POST['indicaciones']);
    $recomendaciones = trim($_POST['recomendaciones']);
    $id_paciente = trim($_POST['id_paciente']);
    $id_cita = trim($_POST['id_cita']);
    $id_usuario = $_SESSION['idUsuario']; // ID del usuario que procesa la cita
    $fecha_atnc = date('Y-m-d H:i:s');
    $estado = 'PROCESADA';
    

    // Preparar la consulta SQL
    $sql = 'UPDATE citas_medicas SET estado = ?, id_usuario = ?, presion_arterial = ?, frecuencia_cardiaca = ?, frecuencia_respiratoria = ?, saturacion_oxigeno = ?, peso = ?, talla = ?, imc = ?,temperatura = ?, diagnostico = ?, indicaciones = ?, recomendaciones = ?, fecha_atnc = ?
            WHERE id_cita = ?';
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 'siddddddddssssi', $estado, $id_usuario, $presion_arterial, $frecuencia_cardiaca, $frecuencia_respiratoria, $saturacion_oxigeno, $peso, $talla, $imc, $temperatura, $diagnostico, $indicaciones, $recomendaciones, $fecha_atnc, $id_cita);

        if (mysqli_stmt_execute($stmt)) {
            // Obtener el ID del usuario recién insertado
            $id_usuario = mysqli_insert_id($link); // Obtiene el ID generado automáticamente

            header('Location: receta_citas.php?id_cita=' . $id_cita . '&mensaje=Cita+procesada+correctamente');

            exit();
        } else {
            echo "Error al actualizar cita: " . mysqli_error($link);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error al preparar la consulta: " . mysqli_error($link);
    }
} else {
    echo "Acceso no autorizado.";
}

mysqli_close($link);
?>