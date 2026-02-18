<?php
session_start();

header('Content-Type: application/json');

// 🔐 Validar sesión
if (empty($_SESSION['usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado']);
    exit;
}

// 🔐 Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

// 🔐 Validar formato token
if (empty($_POST['token']) || !preg_match('/^[a-f0-9]{32}$/', $_POST['token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Token inválido']);
    exit;
}

$token              = $_POST['token'];
$tipo_documento     = $_POST['tipo_documento'] ?? '';
$numero_documento   = $_POST['numero_documento'] ?? '';

$apellidos          = ucwords(mb_strtolower($_POST['apellidos'] ?? '', 'UTF-8'));
$nombre_completo    = ucwords(mb_strtolower($_POST['nombre_completo'] ?? '', 'UTF-8'));

$fecha_nacimiento   = $_POST['fecha_nacimiento'] ?? null;
$grupo              = $_POST['grupo'] ?? '';
$categoria          = $_POST['categoria'] ?? '';
$dependencia        = $_POST['dependencia'] ?? '';
$fecha_afiliacion   = $_POST['fecha_afiliacion'] ?? null;
$fecha_inactivacion = $_POST['fecha_inactivacion'] ?? null;
$estado             = $_POST['estado'] ?? '';

// 🔐 Validar campos obligatorios
if (
    !$tipo_documento ||
    !$numero_documento ||
    !$apellidos ||
    !$nombre_completo ||
    !$grupo ||
    !$categoria ||
    !$estado
) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos obligatorios']);
    exit;
}

require_once '../includes/conexion.php';

// 🔍 Validar documento duplicado (excepto el mismo afiliado)
$stmt = $conexion->prepare("
    SELECT token 
    FROM afiliados 
    WHERE numero_documento = ? 
    AND token != ?
");

$stmt->bind_param('ss', $numero_documento, $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Número de documento ya registrado en otro afiliado'
    ]);
    exit;
}

$stmt->close();

// 💾 Actualizar afiliado
$stmt = $conexion->prepare("
    UPDATE afiliados 
    SET
        tipo_documento = ?,
        numero_documento = ?,
        apellidos = ?,
        nombre_completo = ?,
        fecha_nacimiento = ?,
        grupo = ?,
        categoria = ?,
        dependencia = ?,
        fecha_afiliacion = ?,
        fecha_inactivacion = ?,
        estado = ?
    WHERE token = ?
");

$stmt->bind_param(
    'ssssssssssss',
    $tipo_documento,
    $numero_documento,
    $apellidos,
    $nombre_completo,
    $fecha_nacimiento,
    $grupo,
    $categoria,
    $dependencia,
    $fecha_afiliacion,
    $fecha_inactivacion,
    $estado,
    $token
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al actualizar en la base de datos'
    ]);
}

$stmt->close();
$conexion->close();
