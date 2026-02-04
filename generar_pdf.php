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


$fecha_nac = new DateTime($paciente['fecha_nacimiento']);
$hoy = new DateTime();
$edad = $hoy->diff($fecha_nac)->y;

// 🔹 Consultar el ID del historial médico del paciente
$stmt_historial = $link->prepare("SELECT id_historial FROM historial_medico WHERE id_paciente = ? LIMIT 1");
$stmt_historial->bind_param("i", $id_paciente);
$stmt_historial->execute();
$result_historial = $stmt_historial->get_result();
$historial = $result_historial->fetch_assoc();

$id_historial = $historial ? $historial['id_historial'] : 'Sin historial registrado';


// Consulta citas
$stmt = $link->prepare("SELECT c.*, u.nombre, u.primer_apellido, u.segundo_apellido, u.cedula_profesional FROM citas_medicas AS c INNER JOIN usuarios AS u ON c.id_usuario = u.id_usuario WHERE c.id_paciente = ? AND c.estado ='PROCESADA' ORDER BY c.fecha_cita DESC");
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$result_citas = $stmt->get_result();



// Consulta documentos
$stmt = $link->prepare("SELECT * FROM documentos_digitalizados WHERE id_paciente = ? ORDER BY fecha_subida");
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$result_documentos = $stmt->get_result();

// Crear PDF
$pdf = new TCPDF();
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 7);
$pdf->SetMargins(8, 8, 8, true);

$html = '
<table class="table" style="font-size:100%;">
    <tr>
        <td style="width:20%; vertical-align:middle;">
            <img src="./images/logo.jpg" alt="Logo" style="width:60px; height:auto;">
        </td>

        <td style="width:50%; text-align:center; vertical-align:middle;">
            <h3>Historial Médico</h3>
        </td>
        <td style="width:30%; text-align:right; vertical-align:middle;">
            <h5>Fecha de creación: ' . date('d/m/Y') . '</h5>
            <h5>ID Historial: ' . htmlspecialchars($paciente['curp']) . " - " . $id_historial . '</h5>
            <h5>Fecha de Registro: ' . htmlspecialchars($paciente['fecha_registro']) . '</h5>
        </td>
    </tr>
</table>';
$html .= '
<style>
    .seccion-titulo {
        background-color: #009682;
        color: white;
        font-weight: bold;
        padding: 4px;
        font-size: 9pt;
        margin-top: 9px;
    }
    .dato { font-weight: bold; color: #333; }
    .valor { color: #555; }
</style>
<br>
<div class="seccion-titulo">1. Datos del Paciente</div>

<table cellpadding="2" cellspacing="0" border="0" width="100%">
    <tr>
        <td width="15%" style="text-align:center; vertical-align:middle;">
            <img src="' . htmlspecialchars($paciente['foto']) . '" width="60" height="60" alt="Foto del Paciente"
                 style="border:1px solid #ccc; border-radius:5px;">
        </td>
        
        <td width="40%" style="vertical-align:top;">
            <div><span class="dato">Nombre: </span><span class="valor">' . htmlspecialchars($paciente['nombre']) . ' ' . htmlspecialchars($paciente['primer_apellido']) . ' ' . htmlspecialchars($paciente['segundo_apellido']) . '</span></div>
            <div><span class="dato">Sexo: </span><span class="valor">' . htmlspecialchars($paciente['sexo']) . '</span></div>
            <div><span class="dato">Religión: </span><span class="valor">' . htmlspecialchars($paciente['religion']) . '</span></div>
            <div><span class="dato">Correo: </span><span class="valor">' . htmlspecialchars($paciente['correo']) . '</span></div>
        </td>
        <td width="25%" style="vertical-align:top;">
            <div><span class="dato">Fecha de Nacimiento: </span><span class="valor">' . htmlspecialchars($paciente['fecha_nacimiento']) . '</span></div>
            <div><span class="dato">CURP: </span><span class="valor">' . htmlspecialchars($paciente['curp']) . '</span></div>
            <div><span class="dato">Ocupación: </span><span class="valor">' . htmlspecialchars($paciente['ocupacion']) . '</span></div>
            <div><span class="dato">Derechohabiencia: </span><span class="valor">' . htmlspecialchars($paciente['derechohabiencia']) . '</span></div>    
        </td>
        <td width="35%" style="vertical-align:top;">
            <div><span class="dato">Edad: </span><span class="valor">' . htmlspecialchars($edad) . ' AÑOS</span></div>
            <div><span class="dato">Estado civil: </span><span class="valor">' . htmlspecialchars($paciente['estado_civil']) . '</span></div>
            <div><span class="dato">Teléfono: </span><span class="valor">' . htmlspecialchars($paciente['telefono']) . '</span></div>
        </td>
    </tr>
    <tr>
        <td width="55%" style="vertical-align:top;">
            
            <div><span class="dato">Dirección: </span><span class="valor">' . htmlspecialchars($paciente['direccion']) . '</span></div>     
           
        </td>
        
        <td width="40%" style="vertical-align:top;">
            
            <div><span class="dato">Parentesco: </span><span class="valor">' . htmlspecialchars($paciente['parentesco']) . '</span></div>
             
        </td>
    </tr>
    <tr>
        <td width="25%" style="vertical-align:top;">
            <div><span class="dato">Grupo Sanguíneo: </span><span class="valor">' . htmlspecialchars($paciente['tipo_sangre']) . '</span></div>
        </td>
        <td width="30%" style="vertical-align:top;">
            <div><span class="dato">Alergias: </span><span class="valor">' . htmlspecialchars($paciente['alergias']) . '</span></div>
        </td>
        <td width="25%" style="vertical-align:top;">
        <div><span class="dato">Contacto de Emergencia: </span><span class="valor">' . htmlspecialchars($paciente['nom_emergencia']) . '</span></div>    
        
        </td>
    </tr>
    <tr>

        <td width="55%" style="vertical-align:top;">
        <div><span class="dato">Padecimientos Crónicos: </span><span class="valor">' . htmlspecialchars($paciente['padecimientos']) . '</span></div>
        </td>
        <td width="30%" style="vertical-align:top;">
        <div><span class="dato">Teléfono de Emergencia: </span><span class="valor">' . htmlspecialchars($paciente['telefono_emergencias']) . '</span></div>
            
        </td>
    </tr>
</table>
<br>
<div class="seccion-titulo">2. Citas Médicas</div>';
if ($result_citas->num_rows > 0) {
    $html .= '<table cellpadding="2" cellspacing="0" border="0" width="100%">';
    while ($cita = $result_citas->fetch_assoc()) {
        $presion_arterial = !empty($cita['presion_arterial']) ? htmlspecialchars($cita['presion_arterial']) : 'No hay información';
        $frecuencia_cardiaca = !empty($cita['frecuencia_cardiaca']) ? htmlspecialchars($cita['frecuencia_cardiaca']) . ' bpm' : 'No hay información';
        $frecuencia_respiratoria = !empty($cita['frecuencia_respiratoria']) ? htmlspecialchars($cita['frecuencia_respiratoria']) . ' rpm' : 'No hay información';
        $saturacion_oxigeno = !empty($cita['saturacion_oxigeno']) ? htmlspecialchars($cita['saturacion_oxigeno']) . ' %' : 'No hay información';
        $fecha_cita = !empty($cita['fecha_cita']) ? htmlspecialchars($cita['fecha_cita']) : 'No hay información';
        $hora_cita = !empty($cita['hora_cita']) ? htmlspecialchars($cita['hora_cita']) : 'No hay información';
        $motivo = !empty($cita['motivo']) ? htmlspecialchars($cita['motivo']) : 'No hay información';
        $peso = !empty($cita['peso']) ? htmlspecialchars($cita['peso']) . ' KG' : 'No hay información';
        $imc = !empty($cita['imc']) ? htmlspecialchars($cita['imc']) : 'No hay información';
        $talla = !empty($cita['talla']) ? htmlspecialchars($cita['talla']) . ' M' : 'No hay información';
        $temperatura = !empty($cita['temperatura']) ? htmlspecialchars($cita['temperatura']) . ' °C' : 'No hay información';
        $diagnostico = !empty($cita['diagnostico']) ? htmlspecialchars($cita['diagnostico']) : 'No hay información';
        $indicaciones = !empty($cita['indicaciones']) ? htmlspecialchars($cita['indicaciones']) : 'No hay información';
        $recomendaciones = !empty($cita['recomendaciones']) ? htmlspecialchars($cita['recomendaciones']) : 'No hay información';
        $medico = (!empty($cita['nombre']) || !empty($cita['primer_apellido']) || !empty($cita['segundo_apellido'])) ?
            htmlspecialchars($cita['nombre'] . ' ' . $cita['primer_apellido'] . ' ' . $cita['segundo_apellido']) : 'No hay información';
        $cedula_profesional = !empty($cita['cedula_profesional']) ? htmlspecialchars($cita['cedula_profesional']) : 'No hay información';

        $html .= '
        <tr>
            <td width="20%" style="vertical-align:top;">
                <div><span class="dato">Fecha cita: </span><span class="valor">' . $fecha_cita . '</span></div>
            </td>
            <td width="20%" style="vertical-align:top;">
                <div><span class="dato">Hora cita: </span><span class="valor">' . $hora_cita . '</span></div>
            </td>
            <td width="60%" style="vertical-align:top;">
                <div><span class="dato">Motivo: </span><span class="valor">' . $motivo . '</span></div>
            </td>
        </tr>
        <tr>
            <td width="25%" style="vertical-align:top;">
                <div><span class="dato">Presión arterial: </span><span class="valor">' . $presion_arterial . '</span></div>
            </td>
            <td width="25%" style="vertical-align:top;">
                <div><span class="dato">Frecuencia cardiaca: </span><span class="valor">' . $frecuencia_cardiaca . '</span></div>
            </td>
            <td width="25%" style="vertical-align:top;">
                <div><span class="dato">Frecuencia respiratoria: </span><span class="valor">' . $frecuencia_respiratoria . '</span></div>
            </td>
            <td width="25%" style="vertical-align:top;">
                <div><span class="dato">Saturación de oxígeno: </span><span class="valor">' . $saturacion_oxigeno . '</span></div>
            </td>
        </tr>
        <tr>
            <td width="25%" style="vertical-align:top;">
                <div><span class="dato">Peso: </span><span class="valor">' . $peso . '</span></div>
            </td>
            <td width="25%" style="vertical-align:top;">
                <div><span class="dato">Talla: </span><span class="valor">' . $talla . '</span></div>
            </td>
            
            <td width="25%" style="vertical-align:top;">
                <div><span class="dato">IMC: </span><span class="valor">' . $imc . '</span></div>
            </td>
            <td width="25%" style="vertical-align:top;">
                <div><span class="dato">Temperatura: </span><span class="valor">' . $temperatura . '</span></div>
            </td>
        </tr>
        <tr>
            <td width="100%" style="vertical-align:top;">
                <div><span class="dato">Diagnóstico: </span></div>
            </td>
        </tr>
        <tr>
            <td width="100%" style="vertical-align:top;">
                <span class="valor">' . $diagnostico . '</span>
            </td>
        </tr>
        <tr>
            <td width="100%" style="vertical-align:top;">
                <div><span class="dato">Tratamiento: </span></div>
            </td>
        </tr>
        <tr>
            <td width="100%" style="vertical-align:top;">
                <span class="valor">' . $indicaciones . '</span>
            </td>
        </tr>
         <tr>
            <td width="100%" style="vertical-align:top;">
                <div><span class="dato">Recomendaciones: </span></div>
            </td>
        </tr>
        <tr>
            <td width="100%" style="vertical-align:top;">
                <span class="valor">' . $recomendaciones . '</span>
            </td>
        </tr>
        <tr>
            <td width="50%" style="vertical-align:top;">
                <div><span class="dato">Médico: </span><span class="valor">' . $medico . '</span></div>
            </td>
            <td width="50%" style="vertical-align:top;">
                <div><span class="dato">Cédula profesional: </span><span class="valor">' . $cedula_profesional . '</span></div>
            </td>
        </tr>
        <br>
        <tr><td width="100%"><hr style="border: 1px solid #125873; margin: 10px 0;"></td></tr>
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
