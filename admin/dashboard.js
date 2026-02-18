$(document).ready(function () {
    // Inicializar modal bootstrap
    const editModal = new bootstrap.Modal(
        document.getElementById("editAfiliadoModal"),
    );

    /* =====================================================
     FUNCIÓN NUEVA — CAPITALIZAR TEXTO
     ===================================================== */
    function capitalizarTexto(texto) {
        if (!texto) return "-";
        return texto.toLowerCase().replace(/\b\w/g, (l) => l.toUpperCase());
    }

    /* =====================================================
     BLOQUE CATEGORÍAS (EXISTENTE)
    ===================================================== */
    const categorias = {
        FEMENINO: [
            "Pre-infantil",
            "Infantil",
            "Menores",
            "Juvenil",
            "Mayores A",
            "Mayores B",
        ],
        MASCULINO: [
            "Pre-infantil",
            "Infantil",
            "Menores",
            "Juvenil",
            "Mayores A",
            "Mayores Intermedio",
            "Mayores Fundamentación",
            "Súper Liga",
        ],
    };

    function inicializarCategoriasEdicion() {
        const grupoSelect = document.getElementById("edit_grupo");
        const categoriaSelect = document.getElementById("edit_categoria");

        if (!grupoSelect || !categoriaSelect) return;

        const categoriaActual = (
            categoriaSelect.dataset.categoriaActual || ""
        ).trim();

        function cargarCategorias(grupo) {
            categoriaSelect.innerHTML = '<option value="">Seleccione</option>';

            if (grupo === "No aplica") {
                const option = document.createElement("option");
                option.value = "No aplica";
                option.textContent = "No aplica";
                categoriaSelect.appendChild(option);
                return;
            }

            (categorias[grupo] || []).forEach((cat) => {
                const option = document.createElement("option");
                option.value = cat;
                option.textContent = cat;
                categoriaSelect.appendChild(option);
            });
        }

        if (grupoSelect.value) {
            cargarCategorias(grupoSelect.value);
        }

        grupoSelect.onchange = function () {
            cargarCategorias(this.value);
        };
    }

    function loadAffiliates(filtros = {}) {
        $.ajax({
            url: "../registro/buscar_afiliados.php",
            type: "POST",
            data: filtros,
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    let html = `
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>Foto</th>
                  <th>Tipo Documento</th>
                  <th>Número Documento</th>
                  <th>Apellidos</th>
                  <th>Nombre Completo</th>
                  <th>Fecha Nacimiento</th>
                  <th>Grupo</th>
                  <th>Categoría</th>
                  <th>Dependencia / Rol</th>
                  <th>Fecha Afiliación</th>
                  <th>Fecha Inactivación</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
          `;

                    if (response.data.length > 0) {
                        response.data.forEach((afiliado) => {
                            html += `
                <tr>
                  <td>
                    <img src="../uploads/${afiliado.foto}"
                         alt="Foto"
                         style="width:50px;height:50px;object-fit:cover;border-radius:50%;">
                  </td>
                  <td>${afiliado.tipo_documento}</td>
                  <td>${afiliado.numero_documento}</td>
                  <td>${capitalizarTexto(afiliado.apellidos)}</td>
                  <td>${capitalizarTexto(afiliado.nombre_completo)}</td>
                  <td>${afiliado.fecha_nacimiento ?? "-"}</td>
                  <td>${afiliado.grupo ?? "-"}</td>
                  <td>${afiliado.categoria ?? "-"}</td>
                  <td>${afiliado.dependencia}</td>
                  <td>${afiliado.fecha_afiliacion}</td>
                  <td>${afiliado.fecha_inactivacion || "-"}</td>
                  <td>${afiliado.estado}</td>
                  <td>
                    <button class="btn btn-sm btn-warning btn-edit"
                            data-token="${afiliado.token}">
                      Editar
                    </button>
                    <button class="btn btn-sm btn-success btn-download-qr"
                            data-token="${afiliado.token}">
                      Descargar QR
                    </button>
                  </td>
                </tr>
              `;
                        });
                    } else {
                        html += `
              <tr>
                <td colspan="13" class="text-center">
                  No se encontraron afiliados.
                </td>
              </tr>
            `;
                    }

                    html += `
              </tbody>
            </table>
          `;

                    $("#results").html(html);
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            },
            error: function () {
                Swal.fire(
                    "Error",
                    "Ocurrió un error al procesar la solicitud.",
                    "error",
                );
            },
        });
    }

    loadAffiliates();

    $("#searchForm").submit(function (e) {
        e.preventDefault();

        const filtros = {
            tipo_documento: $("#tipo_documento").val(),
            numero_documento: $("#numero_documento").val().trim(),
        };

        loadAffiliates(filtros);
    });

    $("#results").on("click", ".btn-download-qr", function () {
        const token = $(this).data("token");

        if (!token) {
            Swal.fire("Error", "Token QR no encontrado.", "error");
            return;
        }

        const url = `../registro/exito.php?token=${encodeURIComponent(token)}`;
        window.open(url, "_blank");
    });

    $("#results").on("click", ".btn-edit", function () {
        const token = $(this).data("token");

        if (!token) {
            Swal.fire("Error", "Token no encontrado.", "error");
            return;
        }

        $("#editFormContent").html(`
      <div class="text-center py-5">
        <div class="spinner-border" role="status"></div>
        <p>Cargando datos...</p>
      </div>
    `);

        $("#edit_token").val(token);
        editModal.show();

        $.ajax({
            url: "../registro/obtener_afiliado.php",
            type: "POST",
            data: { token },
            dataType: "html",
            success: function (html) {
                $("#editFormContent").html(html);
                inicializarCategoriasEdicion();
            },
            error: function () {
                $("#editFormContent").html(
                    '<p class="text-danger text-center">Error al cargar datos.</p>',
                );
            },
        });
    });

    $("#editAfiliadoForm").submit(function (e) {
        e.preventDefault();

        $.ajax({
            url: "../registro/actualizar_afiliado.php",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    Swal.fire(
                        "Éxito",
                        "Afiliado actualizado correctamente.",
                        "success",
                    );
                    editModal.hide();
                    loadAffiliates();
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            },
            error: function () {
                Swal.fire("Error", "Ocurrió un error al actualizar.", "error");
            },
        });
    });
});
