<?php
session_start();

if (empty($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Ushuaia C.V. - Sistema Afiliación</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-4">

        <!-- Encabezado con acciones alineadas por línea -->
        <div class="mb-3">

            <!-- Línea 1 -->
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Ushuaia C.V. Voleibol</h2>

                <button
                    class="btn btn-outline-danger btn-sm"
                    onclick="window.location.href='logout.php'">
                    Cerrar sesión
                </button>
            </div>

            <!-- Línea 2 -->
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sistema De Afiliacion</h5>

                <button
                    class="btn btn-primary btn-sm"
                    onclick="window.location.href='../registro/index.php'">
                    Agregar afiliado
                </button>
            </div>

        </div>

        <!-- Formulario búsqueda compacto (NO SE MUEVE) -->
        <form id="searchForm" class="row g-2 align-items-center mb-4">
            <div class="col-auto">
                <select name="tipo_documento" id="tipo_documento" class="form-select form-select-sm">
                    <option value="">Tipo de documento</option>
                    <option value="CC">CC</option>
                    <option value="TI">TI</option>
                    <option value="Pasaporte">Pasaporte</option>
                </select>
            </div>

            <div class="col-auto">
                <input
                    type="text"
                    name="numero_documento"
                    id="numero_documento"
                    class="form-control form-control-sm"
                    placeholder="Número de documento"
                    autocomplete="off"
                    style="width: 180px;" />
            </div>

            <div class="col-auto ms-auto">
                <button type="submit" class="btn btn-primary btn-sm px-4">
                    Buscar
                </button>
            </div>
        </form>

        <!-- Resultado de búsqueda -->
        <div id="results"></div>
    </div>

    <!-- Modal para editar afiliado -->
    <div class="modal fade" id="editAfiliadoModal" tabindex="-1" aria-labelledby="editAfiliadoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editAfiliadoForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAfiliadoModalLabel">Editar Afiliado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div id="editFormContent">
                            <div class="text-center py-5">
                                <div class="spinner-border" role="status"></div>
                                <p>Cargando datos...</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="hidden" name="token" id="edit_token" />
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="dashboard.js"></script>

</body>

</html>