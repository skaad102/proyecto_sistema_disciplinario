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

  // ===== CÓDIGO DE MODALES DE DOCENTES =====

  // Editar docente
  document.querySelectorAll(".btn-editar").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const especialidad = this.dataset.especialidad;
      const nombres = this.dataset.nombres;

      document.getElementById("cod_docente_editar").value = id;
      document.getElementById("nombres_editar").value = nombres;
      document.getElementById("especialidad_editar").value = especialidad;

      const modal = new bootstrap.Modal(document.getElementById("modalEditar"));
      modal.show();
    });
  });

  // Desactivar docente
  document.querySelectorAll(".btn-desactivar").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const nombres = this.dataset.nombres;

      document.getElementById("cod_docente_desactivar").value = id;
      document.getElementById("nombres_desactivar").textContent = nombres;

      const modal = new bootstrap.Modal(
        document.getElementById("modalDesactivar")
      );
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

      const modal = new bootstrap.Modal(
        document.getElementById("modalActivar")
      );
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

  // Limpiar formulario cuando se cierra el modal
  const modalCrearElement = document.getElementById("modalCrear");
  if (modalCrearElement) {
    console.log("Debug: Modal de creación de docente encontrado");
    modalCrearElement.addEventListener("hidden.bs.modal", function () {
      console.log("Debug: Limpiando formulario de creación de docente");
      document.querySelector("#modalCrear form").reset();
    });
  }

  // ===== FIN CÓDIGO DE MODALES DE DOCENTES =====
});
