<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}
require_once('tcpdf/tcpdf.php');
require_once('conexion.php');

if (!isset($_POST['id_cita'])) {
    die("<script>alert('ID de cita no proporcionado.'); window.close();</script>");
}

$id_cita = $_POST['id_cita'];
$id_usuario = $_SESSION['idUsuario'] ?? null;

// Consulta datos de la cita
$stmt = $link->prepare("SELECT * FROM citas_medicas WHERE id_cita = ?");
$stmt->bind_param("i", $id_cita);
$stmt->execute();
$result_citas = $stmt->get_result();
$cita = $result_citas->fetch_assoc();

if (!$cita) {
    die("<script>alert('Cita no encontrada.'); window.close();</script>");
}

// Consulta citas
$stmt = $link->prepare("SELECT p.*, c.* FROM citas_medicas AS c INNER JOIN pacientes AS p ON c.id_paciente = p.id_paciente WHERE c.id_cita = ? ORDER BY c.fecha_cita DESC");
$stmt->bind_param("i", $id_cita);
$stmt->execute();
$result_usuarios = $stmt->get_result();
$paciente = $result_usuarios->fetch_assoc();

$fecha_nac = new DateTime($paciente['fecha_nacimiento']);
                                    $hoy = new DateTime();
                                    $edad = $hoy->diff($fecha_nac)->y;
                                    echo $edad;

//Consulta médico
$stmt = $link->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result_medico = $stmt->get_result();
$medico = $result_medico->fetch_assoc();

// Crear PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);
$pdf->SetMargins(10, 10, 10);

$html = '
<table class="table" style="font-size:100%;">
    <tr>
        <td style="width:20%; vertical-align:middle;">
            <img src="./images/logo.jpg" alt="Logo" style="width:80px; height:auto;">
        </td>

        <td style="width:50%; text-align:center; vertical-align:middle;">
            <h3>Receta Médica</h3>
        </td>
        <td style="width:30%; text-align:right; vertical-align:middle;">
            <h5>Fecha de creación: ' . date('d/m/Y H:i') . ' HRS.</h5>
        </td>
    </tr>
</table>';
$html .= '
<style>
    .seccion-titulo {
        background-color: #009682;
        color: white;
        font-weight: bold;
        padding: 5px;
        font-size: 10pt;
        margin-top: 10px;
    }
    .dato { font-weight: bold; color: #333; }
    .valor { color: #555; }
</style>
<br>
<div class="seccion-titulo">Datos del Paciente</div>

<table cellpadding="2" cellspacing="0" border="0" width="100%">
    <tr>
    
        <td width="40%" style="vertical-align:top;">
            <div><span class="dato">Nombre: </span><span class="valor">' . htmlspecialchars($paciente['nombre']) . ' ' . htmlspecialchars($paciente['primer_apellido']) . ' ' . htmlspecialchars($paciente['segundo_apellido']) . '</span></div>
            <div><span class="dato">Sexo: </span><span class="valor">' . htmlspecialchars($paciente['sexo']) . '</span></div>
            
        </td>
        <td width="30%" style="vertical-align:top;">
            <div><span class="dato">Fecha de Nacimiento: </span><span class="valor">' . htmlspecialchars($paciente['fecha_nacimiento']) . '</span></div>
            
        </td>
        <td width="15%" style="vertical-align:top;">
            <div><span class="dato">Edad: </span><span class="valor">' . htmlspecialchars($edad) . ' AÑOS</span></div>  
        </td>
        <td width="15%" style="vertical-align:top;">
            <div><span class="dato">Tipo de Sangre: </span><span class="valor">' . htmlspecialchars($paciente['tipo_sangre']) . '</span></div>
        </td>
    </tr>
    <tr>
        <td width="50%" style="vertical-align:top;">
            <div><span class="dato">Padecimientos Crónicos: </span><span class="valor">' . htmlspecialchars($paciente['padecimientos']) . '</span></div>
        </td>    
    </tr>
</table>
<br>
<div class="seccion-titulo">Cita Médica</div>';

$html .= '
       
            <tr>
                <td width="35%" style="vertical-align:top;">
                    <div><span class="dato">Fecha cita: </span><span class="valor">' . htmlspecialchars($cita['fecha_atnc']) . '</span></div>
                </td>
                
                <td width="65%" style="vertical-align:top;">
                    <div><span class="dato">Motivo: </span><span class="valor">' . htmlspecialchars($cita['motivo']) . '</span></div>
                </td>
            </tr>
            <tr>
                <td width="20%" style="vertical-align:top;">
                    <div><span class="dato">Peso: </span><span class="valor">' . htmlspecialchars($cita['peso']) . ' KG</span></div>
                </td>
                <td width="20%" style="vertical-align:top;">
                    <div><span class="dato">Talla: </span><span class="valor">' . htmlspecialchars($cita['talla']) . ' M</span></div>
                </td>
                <td width="20%" style="vertical-align:top;">
                    <div><span class="dato">Temperatura: </span><span class="valor">' . htmlspecialchars($cita['temperatura']) . ' °C</span></div>
                </td>
                
            </tr>
            <tr>
                <td width="100%" style="vertical-align:top;">
                    <div><span class="dato">Diagnóstico: </span></div>
                </td>
            <tr>
                <td width="100%" style="vertical-align:top;">
                    <div><span class="dato">Tratamiento: </span></div>
                </td>
            </tr>
            <tr>
                <td width="100%" style="vertical-align:top;">
                    <span class="valor">' . htmlspecialchars($cita['indicaciones']) . '</span>
                </td>
            </tr>
             <tr>
                <td width="100%" style="vertical-align:top;">
                    <div><span class="dato">Recomendaciones: </span></div>
                </td>
            </tr>
            <tr>
                <td width="100%" style="vertical-align:top;">
                    <span class="valor">' . htmlspecialchars($cita['recomendaciones']) . '</span>
                </td>
            </tr>
            <tr>
                <td width="40%" style="vertical-align:top;">
                    <div><span class="dato">Médico: </span><span class="valor">' . htmlspecialchars($medico['nombre'] . ' ' . $medico['primer_apellido'] . ' ' . $medico['segundo_apellido']) . '</span></div>
                    
                </td>
                <td width="30%" style="vertical-align:top;">
                    <div><span class="dato">Cédula profesional: </span><span class="valor">' . htmlspecialchars($medico['cedula_profesional']) . '</span></div>
                </td>
                <td width="30%" style="vertical-align:top;">
                    <div><span class="dato">Firma:</span></div>
                </td>
            </tr>
            <br>
            ';


$html .= '</table>';


$html .= '<p></p>';


$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
$pdf->Output('receta.pdf', 'I');
exit;
