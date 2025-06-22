<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit();
}

// Conexión a la base de datos
include 'conexion.php';

// Verifica si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_usuario = $_POST['id_usuario'];

    // Validar que el id_usuario no esté vacío
    if (empty($id_usuario)) {
        $_SESSION['mensaje_error'] = "Error: ID de usuario no válido.";
        header("Location: perfil.php");
        exit();
    }

    // Iniciar una transacción por seguridad
    $link->begin_transaction();

    try {
        // Eliminar al usuario
        $sql_delete_usuario = "DELETE FROM usuarios WHERE id_usuario = ?";
        if ($stmt_usuario = $link->prepare($sql_delete_usuario)) {
            $stmt_usuario->bind_param("i", $id_usuario);
            if (!$stmt_usuario->execute()) {
                throw new Exception("Error al eliminar usuario: " . $stmt_usuario->error);
            }
            $stmt_usuario->close();
        }

        // Confirmar la eliminación
        $link->commit();

        // Cerrar sesión y redirigir con mensaje
        
        header("Location: panel.php");
        exit();
    } catch (Exception $e) {
        $link->rollback();
        $_SESSION['mensaje_error'] = $e->getMessage();
        header("Location: perfil.php");
        exit();
    }
} else {
    $_SESSION['mensaje_error'] = "Acceso no autorizado.";
    header("Location: perfil.php");
    exit();
}

$link->close();
?>
