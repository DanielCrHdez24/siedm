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
    $peso = trim($_POST['peso']);
    $talla = trim($_POST['talla']);
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
    $sql = 'UPDATE citas_medicas SET estado = ?, id_usuario = ?, peso = ?, talla = ?, temperatura = ?, diagnostico = ?, indicaciones = ?, recomendaciones = ?, fecha_atnc = ?
            WHERE id_cita = ?';
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, 'sidddssssi', $estado, $id_usuario, $peso, $talla, $temperatura, $diagnostico, $indicaciones, $recomendaciones, $fecha_atnc, $id_cita);

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