<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
include 'conexion.php';

$cita_id_get = $_GET['id_cita'] ?? null;

if ($cita_id_get === null) {
    // Si no viene el id_cita por GET, redirigir a la página de citas
    header("location: citas.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $cita_id_get !== null) {

    $sql_citas = "SELECT c.*, p.*
              FROM citas_medicas c
              INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
              WHERE c.id_cita = ?";
    $stmt_citas = $link->prepare($sql_citas);
    $stmt_citas->bind_param("i", $cita_id_get);
    $stmt_citas->execute();
    $result = $stmt_citas->get_result();
    $citas = $result->fetch_assoc();
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Procesar cita</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles_desktop.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="principal">
    <div class="wrapper">
        <header class="header">
            <a href="#" class="logo"><img src="./images/logo.png" alt="Logo SIEDM" width="150px" /></a>
            <nav class="navbar">
                <a href="panel.php">Dashboard</a>
                <?php
                    // Verifica el rol y redirige a la página correspondiente
                    if ($idRol == 4) {
                        // Si el rol es 4, manda a perfil.php
                        $url = 'perfil.php';
                    } elseif ($idRol == 2 || $idRol == 3) {
                        // Si el rol es 2 o 3, manda a perfil_dif.php
                        $url = 'perfil_dif.php';
                    } else {
                        // Si no es ninguno de los roles especificados, redirige a una página por defecto o muestra un mensaje
                        $url = 'perfil_dif.php';  // Puedes redirigir a una página de error o algo similar
                    }
                    ?>

                    <a href="<?php echo $url; ?>">Mi Perfil</a>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <a href="users.php">Gestión de Usuarios</a>
                <?php endif; ?>
                <a href="citas.php">Gestión de Citas</a>
                <?php if ($idRol == 1 || $idRol == 2 || $idRol == 4): ?>
                    <a href="consultar_historial.php" class="active">Historial Médico</a>
                <?php endif; ?>
                <?php if ($idRol == 1 || $idRol == 2): ?>
                    <a href="configuración.php">Configuración</a>
                <?php endif; ?>
                <a href="logout.php" class="logout-link">Cerrar sesión</a>
                <span style="font-size: 0.7em;">
                    Usuario: <?php echo $_SESSION["nombreUsuario"]; ?>
                </span>
            </nav>
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        </header>

        <div class="container">
            <h2>Cita Médica</h2>
          
                <h3>1. Datos del Paciente</h3>

                <table class="table" style="font-size:80%;">
                    <tbody>
                        <tr>

                            <!-- Aquí la imagen ocupa toda una columna -->
                            <td rowspan="7" style="text-align: center; vertical-align: middle;">

                                <img src="<?php echo htmlspecialchars($citas['foto']); ?>" style="display: block; margin: 0 auto;">

                            </td>
                        </tr>


                        <tr>
                            <th>Nombre</th>
                            <td><?php echo htmlspecialchars($citas['nombre']) . " " . htmlspecialchars($citas['primer_apellido']) . " " . htmlspecialchars($citas['segundo_apellido']); ?></td>
                            <th>Fecha de Nacimiento</th>
                            <td><?php echo htmlspecialchars($citas['fecha_nacimiento']); ?></td>

                            <th>Edad</th>
                            <td> <?php
                                    $fecha_nac = new DateTime($citas['fecha_nacimiento']);
                                    $hoy = new DateTime();
                                    $edad = $hoy->diff($fecha_nac)->y;
                                    echo $edad;
                                    ?> AÑOS</td>
                            <th>Sexo</th>
                            <td><?php echo htmlspecialchars($citas['sexo']); ?></td>

                        </tr>
                        <tr>

                            <th>CURP</th>
                            <td><?php echo htmlspecialchars($citas['curp']); ?></td>
                            <th>Estado Civil</th>
                            <td><?php echo htmlspecialchars($citas['estado_civil']); ?></td>
                            <th>Religión</th>
                            <td><?php echo htmlspecialchars($citas['religion']); ?></td>
                            <th>Ocupación</th>
                            <td><?php echo htmlspecialchars($citas['ocupacion']); ?></td>

                        </tr>
                        <tr>
                            <th>Teléfono</th>
                            <td><?php echo htmlspecialchars($citas['telefono']); ?></td>
                            <th>E-mail</th>
                            <td><?php echo htmlspecialchars($citas['correo']); ?></td>
                            <th>Dirección</th>
                            <td colspan="3"><?php echo htmlspecialchars($citas['direccion']); ?></td>
                        </tr>
                        <tr>
                            <th>Derechohabiencia</th>
                            <td><?php echo htmlspecialchars($citas['derechohabiencia']); ?></td>
                            <th>Contacto emergencia</th>
                            <td><?php echo htmlspecialchars($citas['nom_emergencia']); ?></td>
                            <th>Parentesco</th>
                            <td><?php echo htmlspecialchars($citas['parentesco']); ?></td>
                            <th>Teléfono emergencias</th>
                            <td><?php echo htmlspecialchars($citas['telefono_emergencias']); ?></td>


                        </tr>
                        <tr>
                            <th>Tipo de Sangre</th>
                            <td><?php echo htmlspecialchars($citas['tipo_sangre']); ?></td>
                            <th>Alergias</th>
                            <td><?php echo htmlspecialchars($citas['alergias']); ?></td>
                            <th>Padecimientos Crónicos</th>
                            <td colspan="3"><?php echo htmlspecialchars($citas['padecimientos']); ?></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <h3>2. Detalles de la Cita</h3>
                <table class="table" style="font-size:80%;">
                    <tbody>
                        <tr>
                            <th>Fecha de la Cita</th>
                            <td><?php echo htmlspecialchars($citas['fecha_cita']); ?></td>
                            <th>Hora de la Cita</th>
                            <td><?php echo htmlspecialchars($citas['hora_cita']); ?></td>
                             <th>Motivo de la cita</th>
                            <td><?php echo htmlspecialchars($citas['motivo']); ?></td>
                        </tr>
                    </tbody>
                    </table>
                <br>
                <h3>3. Signos vitales</h3>
                <form method="POST" action="guardar_cita.php">
                    
                     <label for="presion_arterial">Presión arterial:</label>
                    <input type="text" name="presion_arterial" id="presion_arterial" required placeholder="Ingrese la presión arterial Ej. 120/80 mmHg" pattern="^\d+\/\d+$" title="La presión arterial debe ser un número válido en formato sistólica/diastólica.">
                
                    <label for="frecuencia_cardiaca">Frecuencia cardíaca:</label>
                    <input type="text" name="frecuencia_cardiaca" id="frecuencia_cardiaca" required placeholder="Ingrese la frecuencia cardíaca en LATIDOS POR MINUTO Ej. 70 LPM" pattern="^\d+(\.\d{1,2})?$" title="La frecuencia cardíaca debe ser un número válido con hasta dos decimales.">

                    <label for="frecuencia_respiratoria">Frecuencia respiratoria:</label>
                    <input type="text" name="frecuencia_respiratoria" id="frecuencia_respiratoria" required placeholder="Ingrese la frecuencia respiratoria en RESPIRACIONES POR MINUTO Ej. 16 RPM" pattern="^\d+(\.\d{1,2})?$" title="La frecuencia respiratoria debe ser un número válido con hasta dos decimales.">

                    <label for="saturacion_oxigeno">Saturación de oxigeno:</label>
                    <input type="text" name="saturacion_oxigeno" id="saturacion_oxigeno" required placeholder="Ingrese la saturación de oxígeno en PORCENTAJE Ej. 98%" pattern="^\d+(\.\d{1,2})?$" title="La saturación de oxígeno debe ser un número válido con hasta dos decimales.">

                    <label for="peso">Peso:</label>
                    <input type="text" name="peso" id="peso" required placeholder="Ingrese el peso en KILOGRAMOS Ej. 50.45 KG" pattern="^\d+(\.\d{1,2})?$" title="El peso debe ser un número válido con hasta dos decimales.">

                    <label for="talla">Talla:</label>
                    <input type="text" name="talla" id="talla" required placeholder="Ingrese la talla en METROS Ej. 1.75 M" pattern="^\d+(\.\d{1,2})?$" title="La talla debe ser un número válido con hasta dos decimales.">
                   
                    <label for="imc">IMC:</label>
                    <input type="text" name="imc" id="imc" readonly placeholder="El IMC se calcula automáticamente" >

                    <label for="temperatura">Temperatura:</label>
                    <input type="text" name="temperatura" id="temperatura" required placeholder="Ingrese la temperatura en CELSIUS Ej. 36.5 °C" pattern="^\d+(\.\d{1,2})?$" title="La temperatura debe ser un número válido con hasta dos decimales.">

                    <label for="diagnostico">Diagnostico:</label>
                    <textarea name="diagnostico" id="diagnostico" oninput="this.value = this.value.toUpperCase()" required placeholder="Ingrese el diagnóstico del paciente" rows="4" cols="50"></textarea>

                    <label for="indicaciones">Indicaciones:</label>
                    <textarea name="indicaciones" id="indicaciones" oninput="this.value = this.value.toUpperCase()" required placeholder="Ingrese las indicaciones y medicamento para el paciente" rows="4" cols="50"></textarea>

                    <label for="recomendaciones">Recomendaciones:</label>
                    <textarea name="recomendaciones" id="recomendaciones" oninput="this.value = this.value.toUpperCase()" required placeholder="Ingrese las recomendaciones para el paciente" rows="4" cols="50"></textarea>

                    <input type="hidden" name="id_paciente" value="<?php echo htmlspecialchars($citas['id_paciente']); ?>">
                    <input type="hidden" name="id_cita" value="<?php echo htmlspecialchars($citas['id_cita']); ?>">
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <button type="reset" class="btn">
                        <i class="fas fa-undo"></i> Limpiar
                    </button>
                </form>

        </div>
        <br>
        <footer class="footer">
            <p>Daniel Cruz Hernández - 22300104</p>
            <p>Nicolás Misael López Cruz - 22300149</p>
            <p>Karen Elizabeth Patlán Villareal - 22300138</p>
            <p>Irma Rafael Soto - 18100213</p>
            <p>&copy; 2025 - SIEDM</p>
        </footer>
    </div>
    <script src="js/menu.js"></script>
    <script>
function calcularIMC() {
    let peso = parseFloat(document.getElementById("peso").value);
    let talla = parseFloat(document.getElementById("talla").value);

    if (peso > 0 && talla > 0) {
        let imc = peso / (talla * talla);
        document.getElementById("imc").value = imc.toFixed(2);
    }
}

// Ejecutar cada vez que se cambian los valores
document.getElementById("peso").addEventListener("input", calcularIMC);
document.getElementById("talla").addEventListener("input", calcularIMC);
</script>
</body>

</html>