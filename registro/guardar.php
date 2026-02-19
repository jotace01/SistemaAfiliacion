<?php 
require '../includes/conexion.php';
require_once '../libs/phpqrcode/qrlib.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Acceso no permitido');
}

// 📥 Datos obligatorios
$tipo_documento   = $_POST['tipo_documento'];
$numero_documento = trim($_POST['numero_documento']);

if (!ctype_digit($numero_documento)) {
    header("Location: index.php?error=documento");
    exit;
}

// 🔧 NORMALIZAR TEXTO
$apellidos        = ucwords(mb_strtolower($_POST['apellidos'], 'UTF-8'));
$nombre_completo  = ucwords(mb_strtolower($_POST['nombre'], 'UTF-8'));

$fecha_nacimiento = $_POST['fecha_nacimiento'];
$grupo            = $_POST['grupo'];
$categoria        = $_POST['categoria'];
$dependencia      = $_POST['dependencia'];
$fecha_af         = $_POST['fecha_afiliacion'];
$fecha_in         = !empty($_POST['fecha_inactivacion']) ? $_POST['fecha_inactivacion'] : null;
$estado           = $_POST['estado'];

// 🔍 Verificar documento duplicado
$checkSql = "SELECT id FROM afiliados WHERE numero_documento = ?";
$checkStmt = $conexion->prepare($checkSql);
$checkStmt->bind_param("s", $numero_documento);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    header("Location: index.php?duplicado=1");
    exit;
}

// 🔑 Token único
$token = bin2hex(random_bytes(16));

// 📷 Foto
$foto = $_FILES['foto'];

$permitidos = ['image/jpeg', 'image/png'];
$maxSize = 3 * 1024 * 1024; // 3MB

if ($foto['error'] !== 0) {
    header("Location: index.php?error=subida");
    exit;
}

if ($foto['size'] > $maxSize) {
    header("Location: index.php?error=tamano");
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($foto['tmp_name']);

if (!in_array($mime, $permitidos)) {
    header("Location: index.php?error=formato");
    exit;
}

// Generar nombre seguro basado en token
$extension = ($mime === 'image/png') ? 'png' : 'jpg';
$nombreFoto = $token . '.' . $extension;
$rutaFoto = '../uploads/' . $nombreFoto;

if (!move_uploaded_file($foto['tmp_name'], $rutaFoto)) {
    header("Location: index.php?error=subida");
    exit;
}

// 🔲 Generar QR
$urlVerificacion = "https://sistemaafiliacion-production.up.railway.app/verificacion/?token=" . $token;

$rutaQR = "../qrs/qr_" . $token . ".png";

QRcode::png($urlVerificacion, $rutaQR, QR_ECLEVEL_L, 6);

// 💾 Insertar en BD
$sql = "INSERT INTO afiliados 
(
    tipo_documento,
    numero_documento,
    apellidos,
    nombre_completo,
    fecha_nacimiento,
    grupo,
    categoria,
    dependencia,
    fecha_afiliacion,
    fecha_inactivacion,
    estado,
    token,
    foto
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "sssssssssssss",
    $tipo_documento,
    $numero_documento,
    $apellidos,
    $nombre_completo,
    $fecha_nacimiento,
    $grupo,
    $categoria,
    $dependencia,
    $fecha_af,
    $fecha_in,
    $estado,
    $token,
    $nombreFoto
);

if ($stmt->execute()) {
    header("Location: exito.php?token=$token");
    exit;
} else {
    header("Location: index.php?error=subida");
    exit;
}
