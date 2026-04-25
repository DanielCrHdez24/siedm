<?php
session_start();

// Validar sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit();
}

$idRol = $_SESSION['idRol'];
if ($idRol != 1 && $idRol != 2 && $idRol != 3) {
    header("location: index.php");
    exit();
}

require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $required_fields = ['nombre', 'primer_apellido', 'segundo_apellido', 'correo', 'telefono', 'curp', 'sexo', 'fecha_nacimiento', 'derechohabiencia', 'direccion', 'tipo_sangre', 'ocupacion'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            header("location: info_paciente.php?error=Campo+$field+obligatorio");
            exit();
        }
    }


    $correo = trim($_POST['correo']);
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("location: info_paciente.php?error=Correo+inválido");
        exit();
    }

    $telefono = trim($_POST['telefono']);
    if (!preg_match('/^[0-9]{10}$/', $telefono)) {
        header("location: info_paciente.php?error=Teléfono+inválido");
        exit();
    }

    $curp = trim($_POST['curp']);
    if (!preg_match('/^[A-Z]{4}\d{6}[HM][A-Z]{5}[0-9A-Z]\d$/', $curp)) {
        header("location: info_paciente.php?error=CURP+inválida");
        exit();
    }


    if (!empty($_FILES['foto']['name'])) {
        $tmp_name = $_FILES['foto']['tmp_name'];
        $file_name = basename($_FILES['foto']['name']);
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_type, $allowed_types)) {
            $fotoPath = 'uploads/' . uniqid() . "_" . $file_name;
            if (!move_uploaded_file($tmp_name, $fotoPath)) {
                header("location: info_paciente.php?error=Error+al+subir+la+imagen.");
                exit();
            }
        } else {
            header("location: info_paciente.php?error=Error:+Solo+se+permiten+imágenes+JPG,+PNG+o+GIF.");
            exit();
        }
    }

    $nombre = trim($_POST['nombre']);
    $primer_apellido = trim($_POST['primer_apellido']);
    $segundo_apellido = trim($_POST['segundo_apellido']);
    $telefono_emergencias = trim($_POST['telefono_emergencias'] ?? '');
    $sexo = trim($_POST['sexo']);
    $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
    $derechohabiencia = trim($_POST['derechohabiencia']);
    $direccion = trim($_POST['direccion']);
    $tipo_sangre = trim($_POST['tipo_sangre']);
    $religion = trim($_POST['religion'] ?? '');
    $ocupacion = trim($_POST['ocupacion']);
    $alergias = trim($_POST['alergias'] ?? '');
    $padecimientos = trim($_POST['padecimientos'] ?? '');
    $estado_civil = trim($_POST['estado_civil'] ?? '');
    $nom_emergencia = trim($_POST['nom_emergencia'] ?? '');
    $parentesco = trim($_POST['parentesco'] ?? '');

    //Se valida la CURP que no exista previamente en la base de datos
    $stmt_check = $link->prepare("SELECT id_paciente FROM pacientes WHERE curp = ?");
    $stmt_check->bind_param("s", $curp);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        header("location: info_paciente.php?error=CURP+ya+registrada");
        exit();
    }
    $stmt_check->close();


    $link->begin_transaction();

    // INSERT PACIENTE
    $sql = "INSERT INTO pacientes 
    (foto,nombre,primer_apellido,segundo_apellido,correo,telefono,telefono_emergencias,curp,sexo,fecha_nacimiento,derechohabiencia,direccion,tipo_sangre,religion,ocupacion,alergias,padecimientos,estado_civil,nom_emergencia,parentesco,fecha_registro) 
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?, NOW())";

    $stmt = $link->prepare($sql);

    $stmt->bind_param(
        "ssssssssssssssssssss",
        $fotoPath,
        $nombre,
        $primer_apellido,
        $segundo_apellido,
        $correo,
        $telefono,
        $telefono_emergencias,
        $curp,
        $sexo,
        $fecha_nacimiento,
        $derechohabiencia,
        $direccion,
        $tipo_sangre,
        $religion,
        $ocupacion,
        $alergias,
        $padecimientos,
        $estado_civil,
        $nom_emergencia,
        $parentesco
    );

    if (!$stmt->execute()) {
        $link->rollback();
        header("location: info_paciente.php?error=Error+al+insertar+paciente");
        exit();
    }

    $id_paciente = $stmt->insert_id;
    $stmt->close();

    // Inserta historial médico vacío para el paciente
    $stmt_hist = $link->prepare("INSERT INTO historial_medico (id_paciente, fecha_creacion) VALUES (?, NOW())");
    $stmt_hist->bind_param("i", $id_paciente);

    if (!$stmt_hist->execute()) {
        $link->rollback();
        header("location: info_paciente.php?error=Error+al+crear+historial");
        exit();
    }

    $stmt_hist->close();


    $link->commit();
    $link->close();

    header("Location: paciente.php?id_paciente=$id_paciente&mensaje=Paciente+creado");
    exit();
}
