<?php
// descargar_qr.php
require_once '../includes/conexion.php';
require_once '../libs/phpqrcode/qrlib.php';  // Asegúrate que esta ruta sea correcta

if (empty($_GET['token'])) {
    die('Token no especificado');
}

$token = $_GET['token'];

$conn = $conexion;

// Obtener datos para generar QR (por ejemplo, el token o url con info)
$stmt = $conn->prepare("SELECT token FROM afiliados WHERE token = ?");
$stmt->bind_param('s', $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Afiliado no encontrado');
}

$data = $result->fetch_assoc();
$contenidoQR = $data['token']; // Por ejemplo el token como contenido del QR

// Ruta donde guardar el QR temporalmente
$tempDir = sys_get_temp_dir();
$fileName = 'qr_' . $token . '.png';
$filePath = $tempDir . DIRECTORY_SEPARATOR . $fileName;

// Generar QR (si quieres que se genere siempre, o verifica si ya existe para evitar regenerar)
QRcode::png($contenidoQR, $filePath, QR_ECLEVEL_L, 5);

// Enviar archivo para descargar
header('Content-Description: File Transfer');
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="codigo_qr_' . $token . '.png"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public');
readfile($filePath);

// Eliminar archivo temporal si quieres
unlink($filePath);
exit;
