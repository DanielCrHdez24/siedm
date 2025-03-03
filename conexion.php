<?php
    define('DB_SERVER','localhost');
    define('DB_USERNAME','root');
    define('DB_PASSWORD','');
    define('DB_NAME','siedm');

    $link = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_NAME);

    if ($link === false) {
        die('Error en la conexión'. mysqli_connect_error());
    }
?>

<?php
// Conexión a la base de datos
include 'conexion.php';

if (isset($_GET['claveExpediente']) || isset($_GET['curp'])) {
    $claveExpediente = $_GET['claveExpediente'];
    $curp = $_GET['curp'];

    // Comenzamos la consulta
    $sql = "SELECT * FROM expedientes WHERE claveExpediente = ? OR curp = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('ss', $claveExpediente, $curp);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Mostrar los resultados
        while ($row = $resultado->fetch_assoc()) {
            echo "Expediente encontrado: <br>";
            echo "Nombre: " . $row['nombrePaciente'] . "<br>";
            // Muestra más datos del expediente aquí
        }
    } else {
        echo "No se encontraron expedientes.";
    }
}
?>