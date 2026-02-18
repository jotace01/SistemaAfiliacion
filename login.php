<?php
session_start();

require 'includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuario = $_POST['usuario'] ?? '';
    $clave   = $_POST['clave'] ?? '';

    $sql = "SELECT password_hash FROM admin WHERE usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $admin = $resultado->fetch_assoc();

        if (password_verify($clave, $admin['password_hash'])) {
            session_regenerate_id(true); // 🔐 Protección contra session fixation
            $_SESSION['usuario'] = $usuario;
            header("Location: admin/dashboard.php");
            exit;
        }
    }

    header("Location: login.php?error=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login | Sistema de Afiliación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body class="d-flex justify-content-center align-items-center vh-100">

    <div class="login-card p-4">
        <h3 class="text-center mb-1">Ushuaia C.V.</h3>
        <p class="text-center text-primary fw-bold mb-4">Sistema de Afiliación</p>

        <?php if (isset($_GET['error'])): ?>
            <div id="errorMessage" class="alert alert-danger text-center">
                Usuario o contraseña incorrectos
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="usuario" class="form-control" placeholder="Usuario" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="clave" class="form-control" placeholder="Contraseña" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Ingresar
            </button>
        </form>
    </div>

</body>

</html>