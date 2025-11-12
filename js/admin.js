// Guardar la tabla activa en localStorage
function saveActiveTable(tableNumber) {
  localStorage.setItem("activeTableAdmin", tableNumber);
}

// Restaurar la tabla activa INMEDIATAMENTE (antes de que se renderice)
(function () {
  const activeTable = localStorage.getItem("activeTableAdmin");
  // Si no hay tabla guardada, no hacer nada (mostrar el mensaje de bienvenida)
  if (activeTable && activeTable !== "0") {
    // Ocultar table0 y mostrar la tabla guardada ANTES de que se vea
    const table0 = document.getElementById("table0");
    const savedTable = document.getElementById("table" + activeTable);

    if (table0 && savedTable) {
      table0.classList.remove("active");
      savedTable.classList.add("active");
    }
  }
})();

function toggleMenu() {
  const hamburger = document.querySelector(".hamburger");
  const sidebar = document.querySelector(".sidebar");
  const overlay = document.querySelector(".overlay");

  hamburger.classList.toggle("active");
  sidebar.classList.toggle("active");
  overlay.classList.toggle("active");
}

// Cerrar el menú si está abierto
function closeMenu() {
  const hamburger = document.querySelector(".hamburger");
  const sidebar = document.querySelector(".sidebar");
  const overlay = document.querySelector(".overlay");

  hamburger.classList.remove("active");
  sidebar.classList.remove("active");
  overlay.classList.remove("active");
}

function showTable(tableNumber, skipMenuClose = false) {
  // Cerrar menú al inicio
  closeMenu();

  const sections = document.querySelectorAll(".table-section");
  sections.forEach((section) => section.classList.remove("active"));

  const selectedSection = document.getElementById("table" + tableNumber);
  if (selectedSection) {
    selectedSection.classList.add("active");
  }

  const menuItems = document.querySelectorAll(".menu-item");
  menuItems.forEach((item) => item.classList.remove("active"));

  // Solo marcar el menú si no es la tabla 0 (inicio)
  if (tableNumber > 0) {
    const itemIndex = tableNumber - 1;
    if (menuItems[itemIndex]) {
      menuItems[itemIndex].classList.add("active");
    }
  }

  // Guardar la tabla activa
  saveActiveTable(tableNumber);
}

// Restaurar la tabla activa al cargar completamente la página
document.addEventListener("DOMContentLoaded", function () {
  // Limpiar cualquier backdrop residual de Bootstrap al cargar la página
  document
    .querySelectorAll(".modal-backdrop")
    .forEach((backdrop) => backdrop.remove());
  document.body.classList.remove("modal-open");
  document.body.style.overflow = "";
  document.body.style.paddingRight = "";

  // Asegurarse de que el menú esté cerrado al cargar
  closeMenu();

  // Agregar event listener al hamburger menu
  const hamburger = document.querySelector(".hamburger");
  if (hamburger) {
    hamburger.addEventListener("click", function (e) {
      toggleMenu();
    });
  }

  // Agregar event listener al overlay
  const overlay = document.querySelector(".overlay");
  if (overlay) {
    overlay.addEventListener("click", function (e) {
      toggleMenu();
    });
  }

  // ===== CÓDIGO DE MODALES DE ASIGNATURAS =====

  // Editar asignatura
  document.querySelectorAll(".btn-editar").forEach((btn) => {
    btn.addEventListener("click", function () {
      // Verificar si es de asignaturas (tiene data-nombre y data-descripcion)
      if (this.dataset.nombre && this.dataset.descripcion !== undefined) {
        const id = this.dataset.id;
        const nombre = this.dataset.nombre;
        const descripcion = this.dataset.descripcion;

        document.getElementById("cod_asignatura_editar").value = id;
        document.getElementById("nombre_asignatura_editar").value = nombre;
        document.getElementById("descripcion_editar").value = descripcion;

        const modalElement = document.getElementById("modalEditar");
        let modal = bootstrap.Modal.getInstance(modalElement);
        if (!modal) {
          modal = new bootstrap.Modal(modalElement);
        }
        modal.show();
      }
      // Verificar si es de docentes (tiene data-especialidad y data-nombres)
      else if (
        this.dataset.especialidad !== undefined &&
        this.dataset.nombres
      ) {
        const id = this.dataset.id;
        const especialidad = this.dataset.especialidad;
        const nombres = this.dataset.nombres;

        document.getElementById("cod_docente_editar").value = id;
        document.getElementById("nombres_editar").value = nombres;
        document.getElementById("especialidad_editar").value = especialidad;

        const modalElement = document.getElementById("modalEditarDocente");
        let modal = bootstrap.Modal.getInstance(modalElement);
        if (!modal) {
          modal = new bootstrap.Modal(modalElement);
        }
        modal.show();
      }
    });
  });

  // Eliminar asignatura
  document.querySelectorAll(".btn-eliminar").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const nombre = this.dataset.nombre;

      document.getElementById("cod_asignatura_eliminar").value = id;
      document.getElementById("nombre_asignatura_eliminar").textContent =
        nombre;

      const modalElement = document.getElementById("modalEliminar");
      let modal = bootstrap.Modal.getInstance(modalElement);
      if (!modal) {
        modal = new bootstrap.Modal(modalElement);
      }
      modal.show();
    });
  });

  // Limpiar formularios cuando se cierran los modales de ASIGNATURAS
  const modalCrearAsignatura = document.getElementById("modalCrear");
  if (modalCrearAsignatura) {
    modalCrearAsignatura.addEventListener("hidden.bs.modal", function () {
      document.activeElement.blur();
      const form = document.querySelector("#modalCrear form");
      if (form) form.reset();
      setTimeout(() => {
        document
          .querySelectorAll(".modal-backdrop")
          .forEach((backdrop) => backdrop.remove());
        if (!document.querySelector(".modal.show")) {
          document.body.classList.remove("modal-open");
          document.body.style.overflow = "";
          document.body.style.paddingRight = "";
        }
      }, 100);
    });
  }

  const modalEditarAsignatura = document.getElementById("modalEditar");
  if (modalEditarAsignatura) {
    modalEditarAsignatura.addEventListener("hidden.bs.modal", function () {
      document.activeElement.blur();
      const form = document.querySelector("#modalEditar form");
      if (form) form.reset();
      setTimeout(() => {
        document
          .querySelectorAll(".modal-backdrop")
          .forEach((backdrop) => backdrop.remove());
        if (!document.querySelector(".modal.show")) {
          document.body.classList.remove("modal-open");
          document.body.style.overflow = "";
          document.body.style.paddingRight = "";
        }
      }, 100);
    });
  }

  // ===== CÓDIGO DE MODALES DE DOCENTES =====

  // Desactivar docente
  document.querySelectorAll(".btn-desactivar").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const nombres = this.dataset.nombres;

      document.getElementById("cod_docente_desactivar").value = id;
      document.getElementById("nombres_desactivar").textContent = nombres;

      const modalElement = document.getElementById("modalDesactivar");
      let modal = bootstrap.Modal.getInstance(modalElement);
      if (!modal) {
        modal = new bootstrap.Modal(modalElement);
      }
      modal.show();
    });
  });

  // Activar docente
  document.querySelectorAll(".btn-activar").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const nombres = this.dataset.nombres;

      document.getElementById("cod_docente_activar").value = id;
      document.getElementById("nombres_activar").textContent = nombres;

      const modalElement = document.getElementById("modalActivar");
      let modal = bootstrap.Modal.getInstance(modalElement);
      if (!modal) {
        modal = new bootstrap.Modal(modalElement);
      }
      modal.show();
    });
  });

  // Auto-generar nombre de usuario basado en nombres y apellidos
  const nombresInput = document.getElementById("nombres");
  const apellidosInput = document.getElementById("apellidos");
  const usuarioInput = document.getElementById("usuario");

  function generarUsuario() {
    const nombres = nombresInput.value.trim();
    const apellidos = apellidosInput.value.trim();

    if (nombres && apellidos) {
      // Tomar el primer nombre y el primer apellido
      const primerNombre = nombres.split(" ")[0].toLowerCase();
      const primerApellido = apellidos.split(" ")[0].toLowerCase();

      // Generar usuario: nombre.apellido
      const usuario = primerNombre + "." + primerApellido;

      // Remover acentos y caracteres especiales
      const usuarioLimpio = usuario
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z.]/g, "");

      usuarioInput.value = usuarioLimpio;
    }
  }

  // Generar usuario automáticamente cuando se escriben nombres o apellidos
  if (nombresInput && apellidosInput && usuarioInput) {
    nombresInput.addEventListener("blur", generarUsuario);
    apellidosInput.addEventListener("blur", generarUsuario);
  }

  // Limpiar formularios cuando se cierran los modales de DOCENTES
  const modalCrearDocenteElement = document.getElementById("modalCrearUsuario");
  if (modalCrearDocenteElement) {
    modalCrearDocenteElement.addEventListener("hidden.bs.modal", function () {
      document.activeElement.blur();
      const form = document.querySelector("#modalCrearUsuario form");
      if (form) form.reset();
      setTimeout(() => {
        document
          .querySelectorAll(".modal-backdrop")
          .forEach((backdrop) => backdrop.remove());
        if (!document.querySelector(".modal.show")) {
          document.body.classList.remove("modal-open");
          document.body.style.overflow = "";
          document.body.style.paddingRight = "";
        }
      }, 100);
    });
  }

  const modalEditarDocenteElement =
    document.getElementById("modalEditarDocente");
  if (modalEditarDocenteElement) {
    modalEditarDocenteElement.addEventListener("hidden.bs.modal", function () {
      document.activeElement.blur();
      const form = document.querySelector("#modalEditarDocente form");
      if (form) form.reset();
      setTimeout(() => {
        document
          .querySelectorAll(".modal-backdrop")
          .forEach((backdrop) => backdrop.remove());
        if (!document.querySelector(".modal.show")) {
          document.body.classList.remove("modal-open");
          document.body.style.overflow = "";
          document.body.style.paddingRight = "";
        }
      }, 100);
    });
  }

  // Limpiar modal de asignar docente a curso
  const modalAsignarDocenteElement = document.getElementById("asignarDocenteModal");
  if (modalAsignarDocenteElement) {
    modalAsignarDocenteElement.addEventListener("hidden.bs.modal", function () {
      document.activeElement.blur();
      const form = document.querySelector("#asignarDocenteModal form");
      if (form) form.reset();
      // Resetear año lectivo al valor actual
      const anoLectivoInput = document.getElementById("ano_lectivo_asignar");
      if (anoLectivoInput) {
        anoLectivoInput.value = new Date().getFullYear();
      }
      setTimeout(() => {
        document
          .querySelectorAll(".modal-backdrop")
          .forEach((backdrop) => backdrop.remove());
        if (!document.querySelector(".modal.show")) {
          document.body.classList.remove("modal-open");
          document.body.style.overflow = "";
          document.body.style.paddingRight = "";
        }
      }, 100);
    });
  }

  // ===== CÓDIGO DE MODALES DE ESTUDIANTES =====

  // Editar estudiante
  document.querySelectorAll(".btn-editar-estudiante").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;

      // Cargar datos completos del estudiante mediante AJAX
      fetch(`obtener_estudiante.php?id=${id}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            alert("Error al cargar datos del estudiante: " + data.error);
            return;
          }

          // Llenar formulario con datos del usuario
          document.getElementById("cod_estudiante_editar").value =
            data.cod_estudiante;
          document.getElementById("id_tipo_documento_editar_estudiante").value =
            data.id_tipo_documento;
          document.getElementById("numero_documento_editar_estudiante").value =
            data.numero_documento;
          document.getElementById("nombres_editar_estudiante").value =
            data.nombres;
          document.getElementById("apellidos_editar_estudiante").value =
            data.apellidos;
          document.getElementById("telefono_editar_estudiante").value =
            data.telefono;
          document.getElementById("correo_editar_estudiante").value =
            data.correo;
          document.getElementById("direccion_editar_estudiante").value =
            data.direccion || "";
          document.getElementById("usuario_editar_estudiante").value =
            data.usuario;
          document.getElementById("fecha_nacimiento_editar").value =
            data.fecha_nacimiento || "";

          // Mostrar modal
          const modalElement = document.getElementById("modalEditarEstudiante");
          let modal = bootstrap.Modal.getInstance(modalElement);
          if (!modal) {
            modal = new bootstrap.Modal(modalElement);
          }
          modal.show();
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Error al cargar los datos del estudiante id " + id);
        });
    });
  });

  // Auto-generar nombre de usuario basado en nombres y apellidos para ESTUDIANTES
  const nombresEstudianteInput = document.getElementById("nombres_estudiante");
  const apellidosEstudianteInput = document.getElementById(
    "apellidos_estudiante"
  );
  const usuarioEstudianteInput = document.getElementById("usuario_estudiante");

  function generarUsuarioEstudiante() {
    const nombres = nombresEstudianteInput.value.trim();
    const apellidos = apellidosEstudianteInput.value.trim();

    if (nombres && apellidos) {
      const primerNombre = nombres.split(" ")[0].toLowerCase();
      const primerApellido = apellidos.split(" ")[0].toLowerCase();
      const usuario = primerNombre + "." + primerApellido;

      const usuarioLimpio = usuario
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-z.]/g, "");

      usuarioEstudianteInput.value = usuarioLimpio;
    }
  }

  if (
    nombresEstudianteInput &&
    apellidosEstudianteInput &&
    usuarioEstudianteInput
  ) {
    nombresEstudianteInput.addEventListener("blur", generarUsuarioEstudiante);
    apellidosEstudianteInput.addEventListener("blur", generarUsuarioEstudiante);
  }

  // Limpiar formularios cuando se cierran los modales de ESTUDIANTES
  const modalCrearEstudianteElement = document.getElementById(
    "modalCrearEstudiante"
  );
  if (modalCrearEstudianteElement) {
    modalCrearEstudianteElement.addEventListener(
      "hidden.bs.modal",
      function () {
        document.activeElement.blur();
        const form = document.querySelector("#modalCrearEstudiante form");
        if (form) form.reset();
        setTimeout(() => {
          document
            .querySelectorAll(".modal-backdrop")
            .forEach((backdrop) => backdrop.remove());
          if (!document.querySelector(".modal.show")) {
            document.body.classList.remove("modal-open");
            document.body.style.overflow = "";
            document.body.style.paddingRight = "";
          }
        }, 100);
      }
    );
  }

  const modalEditarEstudianteElement = document.getElementById(
    "modalEditarEstudiante"
  );
  if (modalEditarEstudianteElement) {
    modalEditarEstudianteElement.addEventListener(
      "hidden.bs.modal",
      function () {
        document.activeElement.blur();
        const form = document.querySelector("#modalEditarEstudiante form");
        if (form) form.reset();
        setTimeout(() => {
          document
            .querySelectorAll(".modal-backdrop")
            .forEach((backdrop) => backdrop.remove());
          if (!document.querySelector(".modal.show")) {
            document.body.classList.remove("modal-open");
            document.body.style.overflow = "";
            document.body.style.paddingRight = "";
          }
        }, 100);
      }
    );
  }

  // ===== CÓDIGO DE MODALES DE CURSOS =====

  // Editar curso
  document.querySelectorAll(".btn-editar-curso").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;

      // Cargar datos completos del curso mediante AJAX
      fetch(`obtener_curso.php?id=${id}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            alert("Error al cargar datos del curso: " + data.error);
            return;
          }

          // Llenar formulario con datos del curso
          document.getElementById("cod_curso_editar").value = data.cod_curso;
          document.getElementById("id_grado_editar").value = data.id_grado;
          document.getElementById("id_director_grupo_editar").value =
            data.id_director_grupo;
          document.getElementById("ano_lectivo_editar").value =
            data.ano_lectivo;
          document.getElementById("estado_editar").value = data.estado;

          // Mostrar modal
          const modalElement = document.getElementById("modalEditarCurso");
          let modal = bootstrap.Modal.getInstance(modalElement);
          if (!modal) {
            modal = new bootstrap.Modal(modalElement);
          }
          modal.show();
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Error al cargar los datos del curso id " + id);
        });
    });
  });

  // Desactivar curso
  document.querySelectorAll(".btn-desactivar-curso").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const nombre = this.dataset.nombre;

      document.getElementById("cod_curso_desactivar").value = id;
      document.getElementById("nombre_curso_desactivar").textContent = nombre;

      const modalElement = document.getElementById("modalDesactivarCurso");
      let modal = bootstrap.Modal.getInstance(modalElement);
      if (!modal) {
        modal = new bootstrap.Modal(modalElement);
      }
      modal.show();
    });
  });

  // Activar curso
  document.querySelectorAll(".btn-activar-curso").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const nombre = this.dataset.nombre;

      document.getElementById("cod_curso_activar").value = id;
      document.getElementById("nombre_curso_activar").textContent = nombre;

      const modalElement = document.getElementById("modalActivarCurso");
      let modal = bootstrap.Modal.getInstance(modalElement);
      if (!modal) {
        modal = new bootstrap.Modal(modalElement);
      }
      modal.show();
    });
  });

  // Función para filtrar docentes en el selector
  function filtrarDocentes(inputId, selectId) {
    const input = document.getElementById(inputId);
    const select = document.getElementById(selectId);

    if (input && select) {
      input.addEventListener("keyup", function () {
        const filtro = this.value.toLowerCase();
        const opciones = select.options;

        for (let i = 0; i < opciones.length; i++) {
          const opcion = opciones[i];
          if (i === 0) {
            // Mantener la opción "Seleccione..."
            continue;
          }

          const nombre = opcion.getAttribute("data-nombre") || "";
          const documento = opcion.getAttribute("data-documento") || "";

          // Buscar en nombre o documento
          if (
            nombre.includes(filtro) ||
            documento.includes(filtro)
          ) {
            opcion.style.display = "";
          } else {
            opcion.style.display = "none";
          }
        }
      });
    }
  }

  // Aplicar filtro a los selectores de director de grupo
  filtrarDocentes("buscar_director", "id_director_grupo");
  filtrarDocentes("buscar_director_editar", "id_director_grupo_editar");

  // Buscador para la tabla de cursos
  const buscarCurso = document.getElementById("buscarCurso");
  const tablaCursos = document.getElementById("tablaCursos");

  if (buscarCurso && tablaCursos) {
    buscarCurso.addEventListener("keyup", function () {
      const filtro = this.value.toLowerCase();
      const filas = tablaCursos.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

      for (let i = 0; i < filas.length; i++) {
        const fila = filas[i];
        const celdas = fila.getElementsByTagName("td");
        let encontrado = false;

        // Buscar en todas las celdas (grado, director, año)
        for (let j = 0; j < celdas.length; j++) {
          const texto = celdas[j].textContent.toLowerCase();
          if (texto.includes(filtro)) {
            encontrado = true;
            break;
          }
        }

        fila.style.display = encontrado ? "" : "none";
      }
    });
  }

  // ===== CÓDIGO DE MODALES DE ASIGNACIONES =====

  // Buscador de docentes en asignaciones
  filtrarDocentes("buscar_docente_asignar", "id_docente_asignar");

  // Eliminar asignación
  document.querySelectorAll(".btn-eliminar-asignacion").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const info = this.dataset.info;

      document.getElementById("cod_asignacion_eliminar").value = id;
      document.getElementById("info_asignacion_eliminar").textContent = info;

      const modalElement = document.getElementById("modalEliminarAsignacion");
      let modal = bootstrap.Modal.getInstance(modalElement);
      if (!modal) {
        modal = new bootstrap.Modal(modalElement);
      }
      modal.show();
    });
  });

  // Buscador para la tabla de asignaciones
  const buscarAsignacion = document.getElementById("buscarAsignacion");
  const tablaAsignaciones = document.getElementById("tablaAsignaciones");

  if (buscarAsignacion && tablaAsignaciones) {
    buscarAsignacion.addEventListener("keyup", function () {
      const filtro = this.value.toLowerCase();
      const filas = tablaAsignaciones.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

      for (let i = 0; i < filas.length; i++) {
        const fila = filas[i];
        const celdas = fila.getElementsByTagName("td");
        let encontrado = false;

        // Buscar en todas las celdas
        for (let j = 0; j < celdas.length; j++) {
          const texto = celdas[j].textContent.toLowerCase();
          if (texto.includes(filtro)) {
            encontrado = true;
            break;
          }
        }

        fila.style.display = encontrado ? "" : "none";
      }
    });
  }

  // Limpiar formularios cuando se cierran los modales de ASIGNACIONES
  const modalAsignarAsignaturaElement = document.getElementById(
    "modalAsignarAsignatura"
  );
  if (modalAsignarAsignaturaElement) {
    modalAsignarAsignaturaElement.addEventListener(
      "hidden.bs.modal",
      function () {
        document.activeElement.blur();
        const form = document.querySelector("#modalAsignarAsignatura form");
        if (form) form.reset();
        // Limpiar buscador
        const buscador = document.getElementById("buscar_docente_asignar");
        if (buscador) {
          buscador.value = "";
          buscador.dispatchEvent(new Event("keyup")); // Resetear filtro
        }
        setTimeout(() => {
          document
            .querySelectorAll(".modal-backdrop")
            .forEach((backdrop) => backdrop.remove());
          if (!document.querySelector(".modal.show")) {
            document.body.classList.remove("modal-open");
            document.body.style.overflow = "";
            document.body.style.paddingRight = "";
          }
        }, 100);
      }
    );
  }

  // Limpiar formularios cuando se cierran los modales de CURSOS
  const modalCrearCursoElement = document.getElementById("modalCrearCurso");
  if (modalCrearCursoElement) {
    modalCrearCursoElement.addEventListener("hidden.bs.modal", function () {
      document.activeElement.blur();
      const form = document.querySelector("#modalCrearCurso form");
      if (form) form.reset();
      // Limpiar buscador
      const buscador = document.getElementById("buscar_director");
      if (buscador) {
        buscador.value = "";
        buscador.dispatchEvent(new Event("keyup")); // Resetear filtro
      }
      setTimeout(() => {
        document
          .querySelectorAll(".modal-backdrop")
          .forEach((backdrop) => backdrop.remove());
        if (!document.querySelector(".modal.show")) {
          document.body.classList.remove("modal-open");
          document.body.style.overflow = "";
          document.body.style.paddingRight = "";
        }
      }, 100);
    });
  }

  const modalEditarCursoElement = document.getElementById("modalEditarCurso");
  if (modalEditarCursoElement) {
    modalEditarCursoElement.addEventListener("hidden.bs.modal", function () {
      document.activeElement.blur();
      const form = document.querySelector("#modalEditarCurso form");
      if (form) form.reset();
      // Limpiar buscador
      const buscador = document.getElementById("buscar_director_editar");
      if (buscador) {
        buscador.value = "";
        buscador.dispatchEvent(new Event("keyup")); // Resetear filtro
      }
      setTimeout(() => {
        document
          .querySelectorAll(".modal-backdrop")
          .forEach((backdrop) => backdrop.remove());
        if (!document.querySelector(".modal.show")) {
          document.body.classList.remove("modal-open");
          document.body.style.overflow = "";
          document.body.style.paddingRight = "";
        }
      }, 100);
    });
  }

  // ===== FIN CÓDIGO DE MODALES =====
});
