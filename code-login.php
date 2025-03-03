<?php

// Inicializar la sesión
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: panel.php");
    exit();
}

require_once "conexion.php";
$usuario = $password = "";
$usuario_err = $password_err = "";

// Usuario admin temporal para pruebas
$admin_usuario = "admin";
$admin_password = "admin123"; // Contraseña sin hash para comparación

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty(trim($_POST["usuario"]))) {
        $usuario_err = "Por favor, ingrese el usuario.";
    } else {
        $usuario = trim($_POST["usuario"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, ingrese la contraseña.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validando credenciales
    if (empty($usuario_err) && empty($password_err)) {
        // Verificar si el usuario es el admin temporal
        if ($usuario === $admin_usuario && $password === $admin_password) {
            $_SESSION["loggedin"] = true;
            $_SESSION["idUsuario"] = 0; // ID ficticio para el admin temporal
            $_SESSION["nombreUsuario"] = "Administrador";

            header("location: panel.php");
            exit();
        }

        // Si no es el admin temporal, buscar en la base de datos
        $sql = "SELECT idUsuario, nombreUsuario, contrasena FROM usuarios WHERE nombreUsuario = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
        
            mysqli_stmt_bind_param($stmt, "s", $param_usuario);
            $param_usuario = $usuario;
        
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) === 1) {
                    mysqli_stmt_bind_result($stmt, $idUsuario, $nombreUsuario, $clave);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $clave)) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["idUsuario"] = $idUsuario;
                            $_SESSION["nombreUsuario"] = $nombreUsuario;

                            header("location: panel.php");
                            exit();
                        } else {
                            $password_err = "La contraseña es incorrecta.";
                        }
                    }
                } else {
                    $usuario_err = "No se ha encontrado ningún usuario con ese nombre.";
                }
            } else {
                echo "Algo salió mal, inténtelo más tarde.";
            }
        }
    }

    mysqli_close($link);
}

?>
