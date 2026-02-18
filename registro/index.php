<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro de Afiliado</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
        <button
            style="padding: 6px 12px; background-color: #0d6efd; color: #fff; border: none; border-radius: 4px; cursor: pointer;"
            onclick="window.location.href='../admin/dashboard.php'">
            Volver al dashboard
        </button>
    </div>

    <h2>Registro de Afiliado</h2>

    <form action="guardar.php" method="POST" enctype="multipart/form-data">

        <label>Tipo de documento</label>
        <select name="tipo_documento" required>
            <option value="">Seleccione</option>
            <option value="CC">Cédula de ciudadanía</option>
            <option value="TI">Tarjeta de identidad</option>
            <option value="PASAPORTE">Pasaporte</option>
            <option value="CE">Cédula de extranjería</option>
        </select>

        <label>Número de documento</label>
        <input type="number" name="numero_documento" required min="0" step="1">

        <label>Apellidos</label>
        <input type="text" name="apellidos" required>

        <label>Nombre completo</label>
        <input type="text" name="nombre" required>

        <label>Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" required>

        <label>Grupo</label>
        <select name="grupo" id="grupo" required>
            <option value="">Seleccione</option>
            <option value="FEMENINO">Femenino</option>
            <option value="MASCULINO">Masculino</option>
            <option value="No aplica">No aplica</option>
        </select>

        <label>Categoría</label>
        <select name="categoria" id="categoria" required>
            <option value="">Seleccione grupo primero</option>
        </select>

        <label>Dependencia / Rol</label>
        <select name="dependencia" required>
            <option value="">Seleccione</option>
            <option value="Entrenador">Entrenador</option>
            <option value="Deportista">Deportista</option>
            <option value="Administrativo">Administrativo</option>
            <option value="Convenio">Convenio</option>
            <option value="Sistemas">Sistemas</option>
        </select>

        <label>Fecha de afiliación</label>
        <input type="date" name="fecha_afiliacion" required>

        <label>Fecha de inactivación</label>
        <input type="date" name="fecha_inactivacion">

        <label>Estado</label>
        <select name="estado">
            <option value="ACTIVO">ACTIVO</option>
            <option value="INACTIVO">INACTIVO</option>
        </select>

        <label>Foto del afiliado</label>
        <input type="file" name="foto" accept="image/*" required>

        <button type="submit">Guardar afiliado</button>

    </form>

    <script>
        const categorias = {
            FEMENINO: [
                'Pre-infantil',
                'Infantil',
                'Menores',
                'Juvenil',
                'Mayores A',
                'Mayores B'
            ],
            MASCULINO: [
                'Pre-infantil',
                'Infantil',
                'Menores',
                'Juvenil',
                'Mayores A',
                'Mayores Intermedio',
                'Mayores Fundamentación',
                'Súper Liga'
            ]
        };

        const grupoSelect = document.getElementById('grupo');
        const categoriaSelect = document.getElementById('categoria');

        grupoSelect.addEventListener('change', function () {

            categoriaSelect.innerHTML = '<option value="">Seleccione</option>';

            if (this.value === 'No aplica') {
                const option = document.createElement('option');
                option.value = 'No aplica';
                option.textContent = 'No aplica';
                categoriaSelect.appendChild(option);
                categoriaSelect.value = 'No aplica';
                return;
            }

            const opciones = categorias[this.value] || [];

            opciones.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat;
                option.textContent = cat;
                categoriaSelect.appendChild(option);
            });
        });

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('duplicado') === '1') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El número de documento ya está registrado.',
                confirmButtonColor: '#0d6efd'
            });
        }
    </script>
<?php if (isset($_GET['error'])): ?>

<script>
<?php if ($_GET['error'] === 'formato'): ?>
Swal.fire({
    icon: 'error',
    title: 'Formato no permitido',
    text: 'Solo se permiten imágenes JPG o PNG'
});
<?php elseif ($_GET['error'] === 'tamano'): ?>
Swal.fire({
    icon: 'error',
    title: 'Imagen demasiado grande',
    text: 'La imagen supera el tamaño permitido (3MB)'
});
<?php elseif ($_GET['error'] === 'subida'): ?>
Swal.fire({
    icon: 'error',
    title: 'Error al subir la imagen',
    text: 'No se pudo subir la foto'
});
<?php elseif ($_GET['error'] === 'documento'): ?>
Swal.fire({
    icon: 'error',
    title: 'Documento inválido',
    text: 'El número de documento debe contener solo números'
});
<?php endif; ?>
</script>

<?php endif; ?>

</body>
</html>
