<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

// Conexión a la base de datos
include 'conexion.php';

// Verifica si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id_paciente = $_GET['id_paciente'];

    // Validación para asegurarse que el id_paciente esté presente
    if (empty($id_paciente)) {
        echo "Error: ID de paciente no válido.";
        exit();
    }

    // Consultar el ID de usuario asociado al paciente
    $sql_usuario = "SELECT id_usuario FROM pacientes WHERE id_paciente = ?";
    if ($stmt_usuario = mysqli_prepare($link, $sql_usuario)) {
        mysqli_stmt_bind_param($stmt_usuario, "i", $id_paciente);
        mysqli_stmt_execute($stmt_usuario);
        mysqli_stmt_bind_result($stmt_usuario, $id_usuario);

        if (!mysqli_stmt_fetch($stmt_usuario)) {
            echo "Error: No se encontró el paciente.";
            exit();
        }
        mysqli_stmt_close($stmt_usuario);
    } else {
        echo "Error en la consulta de paciente: " . mysqli_error($link);
        exit();
    }

    // Iniciar una transacción para asegurar la consistencia de los datos
    mysqli_begin_transaction($link);

    // Eliminar al paciente de la tabla pacientes
    $sql_paciente = "DELETE FROM pacientes WHERE id_paciente = ?";
    if ($stmt_paciente = mysqli_prepare($link, $sql_paciente)) {
        mysqli_stmt_bind_param($stmt_paciente, "i", $id_paciente);
        if (!mysqli_stmt_execute($stmt_paciente)) {
            // Si falla la eliminación del paciente, deshacer la transacción
            mysqli_rollback($link);
            echo "Error al eliminar paciente: " . mysqli_error($link);
            exit();
        }
        mysqli_stmt_close($stmt_paciente);
    } else {
        // Si hay un error con la consulta de eliminación, deshacer la transacción
        mysqli_rollback($link);
        echo "Error en la consulta de eliminación del paciente: " . mysqli_error($link);
        exit();
    }

    // Eliminar al usuario asociado en la tabla usuarios
    $sql_usuario = "DELETE FROM usuarios WHERE id_usuario = ?";
    if ($stmt_usuario = mysqli_prepare($link, $sql_usuario)) {
        mysqli_stmt_bind_param($stmt_usuario, "i", $id_usuario);
        if (!mysqli_stmt_execute($stmt_usuario)) {
            // Si falla la eliminación del usuario, deshacer la transacción
            mysqli_rollback($link);
            echo "Error al eliminar usuario: " . mysqli_error($link);
            exit();
        }
        mysqli_stmt_close($stmt_usuario);
    } else {
        // Si hay un error con la consulta de eliminación del usuario, deshacer la transacción
        mysqli_rollback($link);
        echo "Error en la consulta de eliminación del usuario: " . mysqli_error($link);
        exit();
    }

    // Si todo salió bien, confirmar la transacción
    mysqli_commit($link);

    // Redirigir después de eliminar
    header("Location: users.php?mensaje=Paciente+eliminado+correctamente");
    exit();
} else {
    echo "Acceso no autorizado.";
}

mysqli_close($link);
?>
