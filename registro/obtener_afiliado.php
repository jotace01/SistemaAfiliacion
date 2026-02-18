<?php
session_start();

// 🔐 Validar sesión
if (empty($_SESSION['usuario'])) {
    http_response_code(403);
    echo 'Acceso no autorizado.';
    exit;
}

// 🔐 Validar método y token
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['token'])) {
    http_response_code(400);
    echo 'Solicitud inválida.';
    exit;
}

// 🔐 Validar formato del token (32 hex)
if (!preg_match('/^[a-f0-9]{32}$/', $_POST['token'])) {
    http_response_code(400);
    echo 'Token inválido.';
    exit;
}

$token = $_POST['token'];

require_once '../includes/conexion.php';

$stmt = $conexion->prepare("
    SELECT 
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
        estado
    FROM afiliados
    WHERE token = ?
    LIMIT 1
");

$stmt->bind_param('s', $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<p class="text-danger text-center">Afiliado no encontrado.</p>';
    exit;
}

$afiliado = $result->fetch_assoc();

$dependencias = [
    'Entrenador',
    'Deportista',
    'Administrativo',
    'Convenio',
    'Sistemas'
];
?>

<div class="mb-3">
    <label class="form-label">Tipo de documento</label>
    <select class="form-select" name="tipo_documento" required>
        <option value="CC" <?= $afiliado['tipo_documento'] === 'CC' ? 'selected' : '' ?>>CC</option>
        <option value="TI" <?= $afiliado['tipo_documento'] === 'TI' ? 'selected' : '' ?>>TI</option>
        <option value="PASAPORTE" <?= $afiliado['tipo_documento'] === 'PASAPORTE' ? 'selected' : '' ?>>Pasaporte</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Número de documento</label>
    <input type="number" class="form-control" name="numero_documento" min="0" step="1" required
        value="<?= htmlspecialchars($afiliado['numero_documento']) ?>" required>
</div>

<div class="mb-3">
    <label class="form-label">Apellidos</label>
    <input type="text" class="form-control" name="apellidos"
        value="<?= htmlspecialchars($afiliado['apellidos']) ?>" required>
</div>

<div class="mb-3">
    <label class="form-label">Nombre completo</label>
    <input type="text" class="form-control" name="nombre_completo"
        value="<?= htmlspecialchars($afiliado['nombre_completo']) ?>" required>
</div>

<div class="mb-3">
    <label class="form-label">Fecha de nacimiento</label>
    <input type="date" class="form-control" name="fecha_nacimiento"
        value="<?= $afiliado['fecha_nacimiento'] ?>" required>
</div>

<div class="mb-3">
    <label class="form-label">Grupo</label>
    <select class="form-select" name="grupo" id="edit_grupo" required>
        <option value="">Seleccione</option>
        <option value="FEMENINO" <?= $afiliado['grupo'] === 'FEMENINO' ? 'selected' : '' ?>>Femenino</option>
        <option value="MASCULINO" <?= $afiliado['grupo'] === 'MASCULINO' ? 'selected' : '' ?>>Masculino</option>
        <option value="No aplica" <?=  $afiliado['grupo'] === 'No aplica' ? 'selected' : '' ?>> No aplica </option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Categoría</label>
    <select class="form-select" name="categoria" id="edit_categoria" required
        data-categoria-actual="<?= htmlspecialchars($afiliado['categoria'] ?? '') ?>">
        
        <option value="">Seleccione</option>
        <option value="No aplica"
            <?= ($afiliado['categoria'] ?? '') === 'No aplica' ? 'selected' : '' ?>>
            No aplica
        </option>

    </select>
</div>


<div class="mb-3">
    <label class="form-label">Dependencia / Rol</label>
    <select class="form-select" name="dependencia" required>
        <?php foreach ($dependencias as $dep): ?>
            <option value="<?= $dep ?>" <?= $afiliado['dependencia'] === $dep ? 'selected' : '' ?>>
                <?= $dep ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Fecha de afiliación</label>
    <input type="date" class="form-control" name="fecha_afiliacion"
        value="<?= $afiliado['fecha_afiliacion'] ?>">
</div>

<div class="mb-3">
    <label class="form-label">Fecha de inactivación</label>
    <input type="date" class="form-control" name="fecha_inactivacion"
        value="<?= $afiliado['fecha_inactivacion'] ?>">
</div>

<div class="mb-3">
    <label class="form-label">Estado</label>
    <select class="form-select" name="estado" required>
        <option value="ACTIVO" <?= $afiliado['estado'] === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
        <option value="INACTIVO" <?= $afiliado['estado'] === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
    </select>
</div>