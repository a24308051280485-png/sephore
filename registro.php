<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "sephora_db");

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($nombre) && !empty($email) && !empty($password)) {
        $nombre_esc = $conexion->real_escape_string($nombre);
        $email_esc = $conexion->real_escape_string($email);
        $pass_esc = $conexion->real_escape_string($password);

        $verificar = $conexion->query("SELECT id FROM usuarios WHERE email = '$email_esc'");

        if ($verificar && $verificar->num_rows > 0) {
            $mensaje = "El correo ya está registrado en la base de datos.";
        } else {
            $sql = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre_esc', '$email_esc', '$pass_esc')";
            
            if ($conexion->query($sql) === TRUE) {
                $_SESSION['usuario_nombre'] = $nombre;
                header("Location: index.html");
                exit();
            } else {
                $mensaje = "Error al guardar en MySQL: " . $conexion->error;
            }
        }
    } else {
        $mensaje = "Por favor llena todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SEPHORA - Registro de Usuarios</title>
    <style>
        body { background-color: white; color: black; font-family: "Times New Roman", serif; margin: 0; padding: 0; }
        .formulario { width: 320px; margin: 50px auto; border: 1px solid black; padding: 20px; text-align: center; }
        input { width: 90%; padding: 8px; margin: 8px 0; border: 1px solid black; box-sizing: border-box; }
        button { background-color: black; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; font-family: "Times New Roman", serif; font-size: 16px; margin-top: 10px; }
        a { color: black; text-decoration: underline; }
        .mensaje { color: red; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="formulario">
        <h2>Crear Cuenta</h2>
        
        <?php if(!empty($mensaje)): ?>
            <p class="mensaje"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <form method="POST" action="registro.php">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
        <br>
        <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a> | <a href="index.php">Ir al Inicio</a>
    </div>
</body>
</html>