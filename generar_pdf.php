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
$stmt = $link->prepare("SELECT * FROM citas_medicas WHERE id_paciente = ? ORDER BY fecha_cita");
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
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);
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
$html .= '<h3>Datos del Paciente</h3>';
$html .= '<table border="1" cellpadding="5">
    <tbody>
        <tr>
            <td rowspan="7" style="text-align: center;">
                <img src="' . htmlspecialchars($paciente['foto']) . '" width="100" height="100">
            </td>
            <th>Clave de Expediente</th>
            <td>' . htmlspecialchars($paciente['clave_expediente']) . '</td>
            <th>Nombre</th>
            <td>' . htmlspecialchars($paciente['nombre']) . ' ' . htmlspecialchars($paciente['primer_apellido']) . ' ' . htmlspecialchars($paciente['segundo_apellido']) . '</td>
            <th>CURP</th>
            <td>' . htmlspecialchars($paciente['curp']) . '</td>
        </tr>
        <tr>
            <th>Edad</th>
            <td>' . htmlspecialchars($paciente['edad']) . '</td>
            <th>Sexo</th>
            <td>' . htmlspecialchars($paciente['sexo']) . '</td>
            <th>Fecha Nac.</th>
            <td>' . htmlspecialchars($paciente['fecha_nacimiento']) . '</td>
        </tr>
        <tr>
            <th>Correo</th>
            <td>' . htmlspecialchars($paciente['correo']) . '</td>
            <th>Teléfono</th>
            <td>' . htmlspecialchars($paciente['telefono']) . '</td>
            <th>Derechohabiencia</th>
            <td>' . htmlspecialchars($paciente['derechohabiencia']) . '</td>
        </tr>
        <tr>
            <th>Dirección</th>
            <td colspan="5">' . htmlspecialchars($paciente['direccion']) . '</td>
        </tr>
        <tr>
            <th>Tipo Sangre</th>
            <td>' . htmlspecialchars($paciente['tipo_sangre']) . '</td>
            <th>Religión</th>
            <td>' . htmlspecialchars($paciente['religion']) . '</td>
            <th>Ocupación</th>
            <td>' . htmlspecialchars($paciente['ocupacion']) . '</td>
        </tr>
        <tr>
            <th>Alergias</th>
            <td>' . htmlspecialchars($paciente['alergias']) . '</td>
            <th>Crónicos</th>
            <td>' . htmlspecialchars($paciente['padecimientos']) . '</td>
            <th>Fecha Registro</th>
            <td>' . htmlspecialchars($paciente['fecha_registro']) . '</td>
        </tr>
    </tbody>
</table>';



$html .= '<h3>Citas Médicas</h3>';
if ($result_citas->num_rows > 0) {
    $html .= '<ul>';
    while ($cita = $result_citas->fetch_assoc()) {
        $html .= '<li>' . htmlspecialchars($cita['fecha_cita']) . ': ' . htmlspecialchars($cita['motivo']) . '</li>';
    }
    $html .= '</ul>';
} else {
    $html .= '<p>No hay citas médicas registradas.</p>';
}

$html .= '<h3>Documentos Digitalizados</h3>';
if ($result_documentos->num_rows > 0) {
    $html .= '<ul>';
    while ($doc = $result_documentos->fetch_assoc()) {
        $html .= '<li>' . htmlspecialchars($doc['nombre_archivo']) . ' - ' . htmlspecialchars($doc['fecha_subida']) . '</li>';
    }
    $html .= '</ul>';
} else {
    $html .= '<p>No hay documentos digitalizados.</p>';
}

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('historial_medico.pdf', 'I');
exit;
