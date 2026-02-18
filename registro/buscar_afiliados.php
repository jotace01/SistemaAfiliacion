<?php
session_start();

header('Content-Type: application/json');

if (empty($_SESSION['usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado']);
    exit;
}

require_once '../includes/conexion.php';

$tipo_documento = $_POST['tipo_documento'] ?? '';
$numero_documento = trim($_POST['numero_documento'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$dependencia = trim($_POST['dependencia'] ?? '');
$fecha_afiliacion = $_POST['fecha_afiliacion'] ?? '';
$fecha_inactivacion = $_POST['fecha_inactivacion'] ?? '';
$estado = $_POST['estado'] ?? '';

try {

    $sql = "SELECT 
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
        foto,
        token
    FROM afiliados
    WHERE 1=1";

    $params = [];
    $types = "";

    if ($tipo_documento !== '') {
        $sql .= " AND tipo_documento = ?";
        $types .= "s";
        $params[] = $tipo_documento;
    }

    if ($numero_documento !== '') {
        $sql .= " AND numero_documento LIKE ?";
        $types .= "s";
        $params[] = "%$numero_documento%";
    }

    if ($nombre !== '') {
        $sql .= " AND nombre_completo LIKE ?";
        $types .= "s";
        $params[] = "%$nombre%";
    }

    if ($dependencia !== '') {
        $sql .= " AND dependencia LIKE ?";
        $types .= "s";
        $params[] = "%$dependencia%";
    }

    if ($fecha_afiliacion !== '') {
        $sql .= " AND fecha_afiliacion = ?";
        $types .= "s";
        $params[] = $fecha_afiliacion;
    }

    if ($fecha_inactivacion !== '') {
        $sql .= " AND fecha_inactivacion = ?";
        $types .= "s";
        $params[] = $fecha_inactivacion;
    }

    if ($estado !== '') {
        $sql .= " AND estado = ?";
        $types .= "s";
        $params[] = $estado;
    }

    $sql .= " ORDER BY nombre_completo ASC LIMIT 100";

    $stmt = $conexion->prepare($sql);

    if ($types !== "") {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $data]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error interno']);
}
