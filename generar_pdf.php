<?php
require_once('tcpdf/tcpdf.php');
require_once('conexion.php');

if (!isset($_POST['id_paciente'])) {
    die("<script>alert('ID de paciente no proporcionado.'); window.close();</script>");
}

$id_paciente = $_POST['id_paciente'];

// Consulta datos del paciente
$stmt = $link->prepare("SELECT * FROM pacientes WHERE id_paciente = ?");
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$result_paciente = $stmt->get_result();
$paciente = $result_paciente->fetch_assoc();

if (!$paciente) {
    die("<script>alert('Paciente no encontrado.'); window.close();</script>");
}

// Consulta citas
$stmt = $link->prepare("SELECT * FROM citas_medicas WHERE id_paciente = ? ORDER BY fecha_cita DESC");
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$result_citas = $stmt->get_result();


//Consulta recetas
$stmt = $link->prepare("SELECT * FROM recetas WHERE id_paciente = ?");
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$result_recetas = $stmt->get_result();

// Consulta documentos
$stmt = $link->prepare("SELECT * FROM documentos_digitalizados WHERE id_paciente = ? ORDER BY fecha_subida");
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$result_documentos = $stmt->get_result();

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
            <h3>Historial Médico</h3>
        </td>
        <td style="width:30%; text-align:right; vertical-align:middle;">
            <h5>Fecha de creación: ' . date('d/m/Y') . '</h5>
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

<div class="seccion-titulo">Datos del Paciente</div>

<table cellpadding="2" cellspacing="0" border="0" width="100%">
    <tr>
        <td width="15%" style="text-align:center; vertical-align:top;">
            <img src="' . htmlspecialchars($paciente['foto']) . '" width="70" height="70" alt="Foto del Paciente"
                 style="border:1px solid #ccc; border-radius:5px;">
        </td>
        <td width="30%" style="vertical-align:top;">
            <div><span class="dato">Nombre: </span><span class="valor">' . htmlspecialchars($paciente['nombre']) . ' ' . htmlspecialchars($paciente['primer_apellido']) . ' ' . htmlspecialchars($paciente['segundo_apellido']) . '</span></div>
            <div><span class="dato">Sexo: </span><span class="valor">' . htmlspecialchars($paciente['sexo']) . '</span></div>
            <div><span class="dato">Teléfono: </span><span class="valor">' . htmlspecialchars($paciente['telefono']) . '</span></div>
        </td>
        <td width="25%" style="vertical-align:top;">
            <div><span class="dato">CURP: </span><span class="valor">' . htmlspecialchars($paciente['curp']) . '</span></div>
            <div><span class="dato">Fecha de Nacimiento: </span><span class="valor">' . htmlspecialchars($paciente['fecha_nacimiento']) . '</span></div>
            <div><span class="dato">Derechohabiencia: </span><span class="valor">' . htmlspecialchars($paciente['derechohabiencia']) . '</span></div>
        </td>
        <td width="30%" style="vertical-align:top;">
            <div><span class="dato">Edad: </span><span class="valor">' . htmlspecialchars($paciente['edad']) . ' años</span></div>
            <div><span class="dato">Correo: </span><span class="valor">' . htmlspecialchars($paciente['correo']) . '</span></div>
            <div><span class="dato">Religión: </span><span class="valor">' . htmlspecialchars($paciente['religion']) . '</span></div>
        </td>
    </tr>
    <tr>
        <td width="70%" style="vertical-align:top;">
            <div><span class="dato">Dirección: </span><span class="valor">' . htmlspecialchars($paciente['direccion']) . '</span></div>
        </td>
        <td width="30%" style="vertical-align:top;">
            <div><span class="dato">Tipo de Sangre: </span><span class="valor">' . htmlspecialchars($paciente['tipo_sangre']) . '</span></div>
        </td>
    </tr>
    <tr>
        <td width="20%" style="vertical-align:top;">
            <div><span class="dato">Ocupación: </span><span class="valor">' . htmlspecialchars($paciente['ocupacion']) . '</span></div>
        </td>
        <td width="25%" style="vertical-align:top;">
            <div><span class="dato">Alergias: </span><span class="valor">' . htmlspecialchars($paciente['alergias']) . '</span></div>
        </td>
        <td width="25%" style="vertical-align:top;">
            <div><span class="dato">Padecimientos Crónicos: </span><span class="valor">' . htmlspecialchars($paciente['padecimientos']) . '</span></div>
        </td>
        <td width="30%" style="vertical-align:top;">
            <div><span class="dato">Fecha de Registro: </span><span class="valor">' . htmlspecialchars($paciente['fecha_registro']) . '</span></div>
        </td>
    </tr>
</table>
<br>
<div class="seccion-titulo">Citas Médicas</div>';
if ($result_citas->num_rows > 0) {
    $html .= '<table cellpadding="2" cellspacing="0" border="0" width="100%">';
    while ($cita = $result_citas->fetch_assoc()) {
        $html .= '
       
            <tr>
                <td width="20%" style="vertical-align:top;">
                    <div><span class="dato">Fecha cita: </span><span class="valor">' . htmlspecialchars($cita['fecha_cita']) . '</span></div>
                </td>
                <td width="20%" style="vertical-align:top;">
                    <div><span class="dato">Hora cita: </span><span class="valor">' . htmlspecialchars($cita['hora_cita']) . '</span></div>
                </td>
                <td width="60%" style="vertical-align:top;">
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
                <td width="40%" style="vertical-align:top;">
                    <div><span class="dato">Diagnostico: </span><span class="valor">' . htmlspecialchars($cita['diagnostico']) . '</span></div>
                </td>
            </tr>
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
            <hr style="border:1px solid #125873; margin:10px 0;"><br>
            ';
    
    }
    $html .= '</table>';
} else {
    $html .= '<p>No hay citas médicas registradas.</p><br>';
}



$html .= '<p></p>';
$html .= '<div class="seccion-titulo">Documentos Digitalizados</div>';

if ($result_documentos->num_rows > 0) {
    $html .= '<table cellpadding="2" cellspacing="0" border="0" width="100%">';
    $html .= '
    <tr>
        <th width="20%" style="font-weight:bold;">Fecha de Subida</th>
        <th width="30%" style="font-weight:bold;">Tipo de documento</th>    
        <th width="40%" style="font-weight:bold;">Nombre del Archivo</th>

    </tr>';
    $html .= '<tbody>';
    while ($doc = $result_documentos->fetch_assoc()) {
        $html .= '
        <tr>
            <td width="20%">' . htmlspecialchars($doc['fecha_subida']) . '</td>
            <td width="30%">' . htmlspecialchars($doc['tipo_documento']) . '</td>
            <td width="40%">' . htmlspecialchars($doc['nombre_archivo']) . '</td>
        </tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';
} else {
    $html .= '<p>No hay documentos digitalizados.</p>';
}
$html .= '<p></p>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('historial_medico.pdf', 'I');
exit;
