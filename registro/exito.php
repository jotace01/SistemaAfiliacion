<?php
require '../includes/conexion.php';

if (!isset($_GET['token'])) {
    exit('Token no proporcionado');
}

$token = $_GET['token'];

// 🔍 Buscar afiliado por token
$sql = "SELECT 
            apellidos,
            nombre_completo,
            grupo,
            categoria,
            dependencia,
            estado
        FROM afiliados 
        WHERE token = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    exit('Afiliado no encontrado');
}

$afiliado = $resultado->fetch_assoc();

// 🔧 FUNCIÓN PARA FORMATO DE TEXTO
function capitalizarTexto($texto) {
    return ucwords(mb_strtolower($texto, 'UTF-8'));
}

// Ruta del QR
$rutaQR = "../qrs/qr_" . $token . ".png";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro exitoso</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/exito.css">

</head>

<body>

    <script>
        Swal.fire({
            icon: 'success',
            title: 'Afiliado registrado correctamente',
            html: `
        <strong>Apellidos:</strong> <?= htmlspecialchars(capitalizarTexto($afiliado['apellidos'])) ?><br>
        <strong>Nombres:</strong> <?= htmlspecialchars(capitalizarTexto($afiliado['nombre_completo'])) ?><br>
        <strong>Grupo:</strong> <?= htmlspecialchars($afiliado['grupo']) ?><br>
        <strong>Categoría:</strong> <?= htmlspecialchars($afiliado['categoria']) ?><br>
        <strong>Dependencia:</strong> <?= htmlspecialchars($afiliado['dependencia']) ?><br>
        <strong>Estado:</strong> <?= htmlspecialchars($afiliado['estado']) ?><br><br>

        <strong>Código QR generado:</strong><br>
        <img src="<?= $rutaQR ?>" width="220">
    `,
            showConfirmButton: true,
            confirmButtonText: 'Registrar otro afiliado'
        }).then(() => {
            window.location.href = 'index.php';
        });
    </script>

</body>

</html>
