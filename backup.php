<?php
session_start();

// =============================
// VALIDAR SESIÓN
// =============================
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

// =============================
// VALIDAR ROL (1 = Administrador)
// =============================
if (!isset($_SESSION['idRol']) || $_SESSION['idRol'] != 1) {
    header("location: panel.php");
    exit();
}

require_once 'conexion.php';

// =============================
// VALIDAR ACCIÓN
// =============================
if (isset($_POST['action']) && $_POST['action'] === 'backup') {

    $nombre_archivo = "backup_" . date("Y-m-d_H-i-s") . ".sql";

    $usuario = $_SESSION['nombreUsuario'];
    $idRol = $_SESSION['idRol'];
    $ip = $_SERVER['REMOTE_ADDR'];

    // =============================
    // GENERAR CONTENIDO EN BUFFER
    // =============================
    ob_start();

    echo "-- ======================================\n";
    echo "-- Backup de la Base de Datos SIEDM\n";
    echo "-- Fecha: " . date("Y-m-d H:i:s") . "\n";
    echo "-- Generado por: $usuario\n";
    echo "-- ======================================\n\n";

    $tablas = $link->query("SHOW TABLES");

    while ($tabla = $tablas->fetch_array()) {

        $nombre_tabla = $tabla[0];

        echo "\n-- --------------------------------------\n";
        echo "-- Estructura de la tabla `$nombre_tabla`\n";
        echo "-- --------------------------------------\n\n";

        echo "DROP TABLE IF EXISTS `$nombre_tabla`;\n";

        $estructura = $link->query("SHOW CREATE TABLE `$nombre_tabla`")->fetch_array();
        echo $estructura[1] . ";\n\n";

        echo "-- Datos de la tabla `$nombre_tabla`\n\n";

        $datos = $link->query("SELECT * FROM `$nombre_tabla`");

        while ($fila = $datos->fetch_assoc()) {

            $campos = array_keys($fila);
            $valores = array_values($fila);

            foreach ($valores as &$valor) {
                if ($valor === null) {
                    $valor = "NULL";
                } else {
                    $valor = "'" . $link->real_escape_string($valor) . "'";
                }
            }

            echo "INSERT INTO `$nombre_tabla` (`" . implode("`, `", $campos) . "`) 
                  VALUES (" . implode(", ", $valores) . ");\n";
        }

        echo "\n";
    }

    // =============================
    // CAPTURAR CONTENIDO Y CALCULAR TAMAÑO
    // =============================
    $contenido = ob_get_clean();
    $tamano_bytes = strlen($contenido);
    $tamano_kb = round($tamano_bytes / 1024, 2) . " KB";

    // =============================
    // REGISTRAR EN TABLA RESPALDOS
    // =============================
    $sql = "INSERT INTO respaldos 
            (nombre_archivo, usuario_genero, rol_usuario, ip_equipo, tamano_archivo)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $link->prepare($sql);

    if (!$stmt) {
        die("Error en prepare: " . $link->error);
    }

    $stmt->bind_param("ssiss", $nombre_archivo, $usuario, $idRol, $ip, $tamano_kb);
    $stmt->execute();
    $stmt->close();

    // =============================
    // ENVIAR ARCHIVO PARA DESCARGA
    // =============================
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
    header('Content-Length: ' . strlen($contenido));
    header('Pragma: no-cache');
    header('Expires: 0');

    echo $contenido;

    $link->close();
    exit();
}
?>
