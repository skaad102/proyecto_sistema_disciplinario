// Guardar la tabla activa en localStorage
function saveActiveTable(tableNumber) {
  localStorage.setItem("activeTableAdmin", tableNumber);
}

// Restaurar la tabla activa INMEDIATAMENTE (antes de que se renderice)
(function () {
  // Si es una nueva sesión Y NO es un submit de formulario, mostrar el mensaje de bienvenida
  if (isNewSession && !isFormSubmit) {
    localStorage.setItem("activeTableAdmin", "0");
    return;
  }

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
  document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
  document.body.classList.remove('modal-open');
  document.body.style.overflow = '';
  document.body.style.paddingRight = '';

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

  // Si no es nueva sesión O es un submit de formulario, verificar y actualizar el menú activo
  if (!isNewSession || isFormSubmit) {
    const activeTable = localStorage.getItem("activeTableAdmin");
    if (activeTable && activeTable !== "0") {
      const menuItems = document.querySelectorAll(".menu-item");
      const itemIndex = parseInt(activeTable) - 1;
      if (menuItems[itemIndex]) {
        menuItems[itemIndex].classList.add("active");
      }
    }
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
      else if (this.dataset.especialidad !== undefined && this.dataset.nombres) {
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
      document.getElementById("nombre_asignatura_eliminar").textContent = nombre;

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
        document.querySelectorAll(".modal-backdrop").forEach((backdrop) => backdrop.remove());
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
        document.querySelectorAll(".modal-backdrop").forEach((backdrop) => backdrop.remove());
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
        document.querySelectorAll(".modal-backdrop").forEach((backdrop) => backdrop.remove());
        if (!document.querySelector(".modal.show")) {
          document.body.classList.remove("modal-open");
          document.body.style.overflow = "";
          document.body.style.paddingRight = "";
        }
      }, 100);
    });
  }

  const modalEditarDocenteElement = document.getElementById("modalEditarDocente");
  if (modalEditarDocenteElement) {
    modalEditarDocenteElement.addEventListener("hidden.bs.modal", function () {
      document.activeElement.blur();
      const form = document.querySelector("#modalEditarDocente form");
      if (form) form.reset();
      setTimeout(() => {
        document.querySelectorAll(".modal-backdrop").forEach((backdrop) => backdrop.remove());
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
