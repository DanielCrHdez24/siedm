
<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}
require_once('tcpdf/tcpdf.php');
require_once('conexion.php');

if (!isset($_POST['id_cita'])) {
    header("location: consultar_cita.php?error=ID_de_cita_no_proporcionado");
    exit();
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
    header("location: consultar_cita.php+?error=Cita_no_encontrada");
    exit();
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


//Consulta médico
$stmt = $link->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result_medico = $stmt->get_result();
$medico = $result_medico->fetch_assoc();

class MYPDF extends TCPDF
{
    public function Footer()
    {
        $this->SetY(-25);
        $this->SetFont('helvetica', 'B', 7);

        // Línea decorativa
        $this->Line(8, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);

        $texto = "HORARIO DE FARMACIA: LUNES A SÁBADO DE 08:00 AM A 9:00 PM Y DOMINGO DE 09:00 AM A 8:00 PM\n"
            . "HORARIO ATENCIÓN MÉDICA: LUNES A VIERNES DE 10:30 AM A 14:30 PM Y 18:30 PM A 20:00 PM\n"
            . "(SÁBADO Y DOMINGO PREVIA CITA)\n"
            . "CONSULTA DE NUTRICIÓN, PSICOLOGÍA Y TERAPIA FÍSICA PREVIA CITA\n"
            . "Tel: 771-688-7206 / 771-688-7395 | Correo: dra.myrnacruz@gmail.com\n"
            . "Página " . $this->getAliasNumPage() . " de " . $this->getAliasNbPages();

        $this->MultiCell(0, 4, $texto, 0, 'C');
    }
}

$html = '
<link rel="icon" href="images/favicon.png" type="image/x-icon">
<table class="table">
    <tr>
        <td style="width:25%; vertical-align:middle;">
            <img src="./images/logo.jpg" alt="Logo" style="width:50px; height:auto;">
        </td>

        <td style="width:50%; text-align:center; vertical-align:middle;">
            <img src="./images/logo_med.png" alt="Logo consultorio" style="width:100px; height:auto;"><br>
            <h3>DRA. ' . htmlspecialchars($medico['nombre']) . ' ' . htmlspecialchars($medico['primer_apellido']) . ' ' . htmlspecialchars($medico['segundo_apellido']) . '</h3>
            <h3>CEDULA PROFESIONAL ' . htmlspecialchars($medico['cedula_profesional']) . '</h3>
            <h4>AV. MIGUEL HIDALGO NO. 605 COL. SANTIAGO TLAPACOYA</h4>
            <h4>PACHUCA DE SOTO, HIDALGO</h4>
            <h4>Receta Médica</h4>
        </td>
        <td style="width:25%; text-align:right; vertical-align:middle;">
            <h5>Fecha: ' . date('d/m/Y H:i') . ' HRS.</h5>
        </td>
    </tr>
</table>';
$html .= '
<style>
body { font-size: 8pt; }
    table { font-size: 8pt; }
    h3 { font-size: 10pt; }
    h4 { font-size: 9pt; }
    h5 { font-size: 8pt; }
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

<table cellpadding="2" cellspacing="0" border="0" width="100%" >
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
                <td width="30%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Fecha cita: </span><span class="valor">' . htmlspecialchars($cita['fecha_atnc']) . '</span></div>
                </td>
                
                <td width="70%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Motivo: </span><span class="valor">' . htmlspecialchars($cita['motivo']) . '</span></div>
                </td>
            </tr>
            <tr>
                <td width="30%" style="vertical-align:top;  font-size: 9pt;">
                    <div><span class="dato">Presión arterial: </span><span class="valor">' . htmlspecialchars($cita['presion_arterial']) . '</span></div>
                </td>
                
                <td width="35%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Frecuencia cardiaca: </span><span class="valor">' . htmlspecialchars($cita['frecuencia_cardiaca']) . ' bpm</span></div>
                </td>
                <td width="35%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Frecuencia respiratoria: </span><span class="valor">' . htmlspecialchars($cita['frecuencia_respiratoria']) . ' rpm</span></div>
                </td>
            </tr>
            <tr>
            <td width="30%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Saturación de oxígeno: </span><span class="valor">' . htmlspecialchars($cita['saturacion_oxigeno']) . '%</span></div>
                </td>
                <td width="15%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Peso: </span><span class="valor">' . htmlspecialchars($cita['peso']) . ' KG</span></div>
                </td>
                <td width="15%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Talla: </span><span class="valor">' . htmlspecialchars($cita['talla']) . ' M</span></div>
                </td>
                <td width="20%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">IMC: </span><span class="valor">' . htmlspecialchars($cita['imc']) . '</span></div>
                </td>
                <td width="20%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Temperatura: </span><span class="valor">' . htmlspecialchars($cita['temperatura']) . ' °C</span></div>
                </td>
                
                
            </tr>
<br>
            <tr>
                <td width="100%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Diagnóstico: </span></div>
                </td>
            </tr>
            <br>
             <tr>
                <td width="100%" style="vertical-align:top; font-size: 9pt;">
                    <span class="valor">' . nl2br(htmlspecialchars($cita['diagnostico'])) . '</span>
                </td>
            </tr>
            <br>
            <tr>
                <td width="100%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Tratamiento: </span></div>
                </td>
            </tr>
            <br>
            <tr>
                <td width="100%" style="vertical-align:top; font-size: 9pt;">
                    <span class="valor">' . nl2br(htmlspecialchars($cita['indicaciones'])) . '</span>
                </td>
            </tr>
            <br>
             <tr>
                <td width="100%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Recomendaciones: </span></div>
                </td>
            </tr>
            <br>
            <tr>
                <td width="100%" style="vertical-align:top; font-size: 9pt;">
                    <span class="valor">' . nl2br(htmlspecialchars($cita['recomendaciones'])) . '</span>
                </td>
            </tr>
            <br><br>
            <tr>
                <td width="40%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Médico: </span><span class="valor">' . htmlspecialchars($medico['nombre'] . ' ' . $medico['primer_apellido'] . ' ' . $medico['segundo_apellido']) . '</span></div>
                    
                </td>
                <td width="30%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Cédula profesional: </span><span class="valor">' . htmlspecialchars($medico['cedula_profesional']) . '</span></div>
                </td>
                <td width="30%" style="vertical-align:top; font-size: 9pt;">
                    <div><span class="dato">Firma: _________________________</span></div>
                </td>
            </tr>
            <br>
            ';


$html .= '</table>';


$html .= '<p></p>';


$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configuración
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->SetMargins(8, 8, 8);
$pdf->SetAutoPageBreak(TRUE, 25);

$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');

ob_end_clean();
$pdf->Output('receta.pdf', 'I');
exit;
