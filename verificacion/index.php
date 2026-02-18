<?php
require '../includes/conexion.php';

if (!isset($_GET['token']) || !preg_match('/^[a-f0-9]{32}$/', $_GET['token'])) {
    exit('Token inválido');
}
$token = $_GET['token'] ?? null;
$afiliado = null;

if ($token) {
    $sql = "SELECT 
                apellidos,
                nombre_completo,
                numero_documento,
                dependencia,
                estado,
                foto 
            FROM afiliados 
            WHERE token = ? 
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $afiliado = $resultado->fetch_assoc();
    }

    $stmt->close();
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Verificación de Afiliación</title>
    <link rel="stylesheet" href="../assets/css/styleTarjeta.css?v=3">
</head>

<body>

    <div class="card">
        <?php if ($afiliado && $afiliado['estado'] === 'ACTIVO'): ?>

            <img src="../assets/img/logoushuaia.jpeg" class="logo-club" alt="Ushuaia C. V. Voleibol">

            <img src="../uploads/<?= htmlspecialchars($afiliado['foto']) ?>" alt="Foto afiliado">

            <h2><?= htmlspecialchars($afiliado['apellidos']) ?></h2>
            <h2><?= htmlspecialchars($afiliado['nombre_completo']) ?></h2>
            <h2><?= htmlspecialchars($afiliado['numero_documento']) ?></h2>
            <h2><?= htmlspecialchars($afiliado['dependencia']) ?></h2>

            <div class="estado activo">AFILIADO ACTIVO</div>

        <?php else: ?>
            <img src="../assets/img/logoushuaia.jpeg" class="logo-club" alt="Ushuaia C. V. Voleibol">            
            <h2>Afiliado no válido</h2>
            <div class="estado inactivo">NO ACTIVO</div>

        <?php endif; ?>
    </div>

    <script src="script.js"></script>
</body>

</html>
