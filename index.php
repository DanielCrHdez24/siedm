<?php
require "code-login.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIEDM - Iniciar Sesión</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
  <div class="container-all">
    <div class="cnt-form">
      <img class="logo" src="images/logo.png" alt="Logo SIEDM">
      <h1 class="title">Iniciar sesión</h1>
      <p></p>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <!-- Grupo Input Correo -->
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input class="form-control" type="email" name="correo" placeholder="Correo electrónico" required>
        </div>
        <span class="msg-error"><?php echo isset($usuario_err) ? htmlspecialchars($usuario_err) : ''; ?></span>

        <!-- Grupo Input Contraseña -->
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input class="form-control" type="password" name="password" placeholder="Contraseña" required>
        </div>
        <span class="msg-error"><?php echo isset($password_err) ? htmlspecialchars($password_err) : ''; ?></span>

        <input class="button" type="submit" value="Aceptar">
      </form>

      <span class="text-footer">¿Necesitas ayuda? Contacta a <a href="#">Soporte</a>.</span>
    </div>

    <div class="cnt-text">
      <div class="capa"></div>
      <h1 class="title-slogan">SIEDM</h1>
      <p class="text-slogan">"Por un historial médico sin papeles y donde la salud se encuentra con la innovación."</p>
    </div>
  </div>
</body>

</html>
