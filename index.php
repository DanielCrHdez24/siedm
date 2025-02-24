<?php
require "code-login.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta
    name="viewport"
    content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, maximum-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="css/styles.css" />
  <title>SIEDM</title>
</head>

<body>
  <div class="container-all">
    <div class="cnt-form">
      <img class="logo" src="images/logo.png" alt="" />
      <h1 class="title">Iniciar sesión</h1>

      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <input class="input-user" type="text" name="usuario" placeholder=" Usuario" />
        <span class="msg-error"><?php echo $usuario_err; ?></span>
        <input class="input-password" type="password" name="password" placeholder=" Contraseña" />
        <span class="msg-error"><?php echo $password_err; ?></span>
        <input class="button" type="submit" value="Aceptar" />

      </form>

      <span class="text-footer">¿Necesitas ayuda? Contacta a
        <a href="#">Soporte</a>.
      </span>
    </div>

    <div class="cnt-text">
      <div class="capa"></div>
      <h1 class="title-slogan">SIEDM</h1>
      <p class="text-slogan">
        "Por un historial médico sin papeles y donde la salud se encuentra con la innovación.."
      </p>
    </div>
  </div>
</body>

</html>