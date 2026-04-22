<?php
// Inicializa la sesión
session_start();

// Si ya está autenticado, redirige al panel
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: panel.php");
    exit();
}

// Incluiye la conexión a la base de datos
require_once "conexion.php";

//  Guarda los datos del formulario y los errores
$correo = $password = "";
$correo_err = $password_err = "";

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Limpia datos de entrada
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($correo)) {
        $correo_err = "Por favor, ingrese el correo.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $correo_err = "Por favor, ingrese un correo electrónico válido.";
    }

    if (empty($password)) {
        $password_err = "Por favor, ingrese la contraseña.";
    }

    // Valida credenciales en la base de datos
    if (empty($correo_err) && empty($password_err)) {
        $sql = "SELECT id_usuario, nombre, primer_apellido, contrasena, id_rol FROM usuarios WHERE correo = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $correo);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) === 1) {
                    mysqli_stmt_bind_result($stmt, $idUsuario, $nombreUsuario, $primerApellido, $clave, $idRol);

                    if (mysqli_stmt_fetch($stmt)) {
                        if ($clave !== null && password_verify($password, $clave)) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["idUsuario"] = $idUsuario;
                            $_SESSION["nombreUsuario"] = $nombreUsuario;
                            $_SESSION["idRol"] = $idRol;

                            // Cierra consulta y redirige al panel
                            mysqli_stmt_close($stmt);
                            header("location: panel.php");
                            exit();
                        } else {
                            $password_err = "La contraseña es incorrecta.";
                        }
                    }
                } else {
                    $correo_err = "No se encontró una cuenta con este correo.";
                }
            } else {
                echo "Error en la consulta, inténtelo más tarde.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
}
