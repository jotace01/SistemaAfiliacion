<?php
require '../includes/conexion.php';

if (!isset($_GET['token'])) {
    exit('Token no proporcionado');
}

$token = $_GET['token'];

$sql = "SELECT nombre_completo, dependencia, estado, foto FROM afiliados WHERE token = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    exit('Afiliado no encontrado');
}

$afiliado = $resultado->fetch_assoc();

$rutaQR = "../qrs/qr_" . $token . ".png";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tarjeta Afiliado - <?= htmlspecialchars($afiliado['nombre_completo']) ?></title>
    <link rel="stylesheet" href="../assets/css/tarjeta.css">
</head>

<body>

    <div class="tarjeta">
        <img class="foto" src="../uploads/<?= htmlspecialchars($afiliado['foto']) ?>" alt="Foto afiliado">
        <h2><?= htmlspecialchars($afiliado['nombre_completo']) ?></h2>
        <p><?= htmlspecialchars($afiliado['dependencia']) ?></p>
        <div class="estado <?= strtolower($afiliado['estado']) ?>">
            <?= htmlspecialchars($afiliado['estado']) ?>
        </div>
        <div class="qr">
            <img src="<?= $rutaQR ?>" alt="Código QR">
        </div>
    </div>

    <script src="tarjeta.js"></script>
</body>

</html>