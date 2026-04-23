<?php
session_start();

// Validar sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit();
}

// Validar rol
if ($_SESSION['idRol'] != 1) {
    header("Location: panel.php");
    exit();
}

// Conexión
require_once 'conexion.php';

// Validar método
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: users.php?error=Acceso+no+autorizado");
    exit();
}

// Validar ID
if (!isset($_POST['id_usuario']) || !is_numeric($_POST['id_usuario'])) {
    header("Location: users.php?error=ID+inválido");
    exit();
}

$id_usuario = (int) $_POST['id_usuario'];

// Evitar auto-eliminación
if ($id_usuario == $_SESSION['idUsuario']) {
    header("Location: users.php?error=No+puedes+desactivarte+a+ti+mismo");
    exit();
}

// Iniciar transacción
$link->begin_transaction();

// Verificar que el usuario existe
$sql_check = "SELECT id_usuario FROM usuarios WHERE id_usuario = ? AND id_rol IN (2,3)";
$stmt_check = $link->prepare($sql_check);

if (!$stmt_check) {
    $link->rollback();
    header("Location: users.php?error=Error+en+la+consulta");
    exit();
}

$stmt_check->bind_param("i", $id_usuario);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows === 0) {
    $stmt_check->close();
    $link->rollback();
    header("Location: users.php?error=Usuario+no+válido");
    exit();
}
$stmt_check->close();

// Desactivar usuario
$sql = "UPDATE usuarios SET estado = 'INACTIVO' WHERE id_usuario = ? AND id_rol IN (2,3)";
$stmt = $link->prepare($sql);

if (!$stmt) {
    $link->rollback();
    header("Location: users.php?error=Error+al+preparar+consulta");
    exit();
}

$stmt->bind_param("i", $id_usuario);

if (!$stmt->execute()) {
    $stmt->close();
    $link->rollback();
    header("Location: users.php?error=Error+al+desactivar+usuario");
    exit();
}

// Validar que sí se actualizó algo
if ($stmt->affected_rows === 0) {
    $stmt->close();
    $link->rollback();
    header("Location: users.php?error=No+se+pudo+desactivar");
    exit();
}

$stmt->close();

// Confirmar cambios
$link->commit();

// Redirigir éxito
header("Location: users.php?mensaje=Usuario+desactivado+correctamente");
exit();

$link->close();
?>