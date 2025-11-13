document.addEventListener("DOMContentLoaded", function () {
  let isNewSession = localStorage.getItem("isNewSession") === "true";
  let isFormSubmit =
    document.querySelector("form") && document.querySelector("form").submitted;

  function saveActiveTable(tableNumber) {
    localStorage.setItem("activeTable", tableNumber);
  }

  function toggleMenu() {
    const hamburger = document.querySelector(".hamburger");
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.querySelector(".overlay");

    if (hamburger && sidebar && overlay) {
      document.body.classList.toggle("menu-open");
      hamburger.classList.toggle("active");
      sidebar.classList.toggle("active");
      overlay.classList.toggle("active");
    }
  }

  function closeMenu() {
    const hamburger = document.querySelector(".hamburger");
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.querySelector(".overlay");

    if (hamburger && sidebar && overlay) {
      hamburger.classList.remove("active");
      sidebar.classList.remove("active");
      overlay.classList.remove("active");
    }
  }

  function showTable(tableNumber) {
    closeMenu();

    const sections = document.querySelectorAll(".table-section");
    sections.forEach((section) => section.classList.remove("active"));

    const selectedSection = document.getElementById("table" + tableNumber);
    if (selectedSection) {
      selectedSection.classList.add("active");
    }

    const menuItems = document.querySelectorAll(".menu-item");
    menuItems.forEach((item) => item.classList.remove("active"));

    if (tableNumber > 0) {
      const itemIndex = tableNumber - 1;
      if (menuItems[itemIndex]) {
        menuItems[itemIndex].classList.add("active");
      }
    }

    saveActiveTable(tableNumber);
  }

  // Initialize menu toggle functionality
  document.querySelector(".hamburger").addEventListener("click", toggleMenu);
  document.querySelector(".overlay").addEventListener("click", toggleMenu);

  // Initialize menu items click handlers
  document.querySelectorAll(".menu-item").forEach((item, index) => {
    item.addEventListener("click", () => showTable(index + 1));
  });

  // Initialize home link
  document
    .querySelector(".welcome")
    .addEventListener("click", () => showTable(0));

  // Load active table from localStorage
  if (isNewSession && !isFormSubmit) {
    localStorage.setItem("activeTable", "0");
  } else {
    const activeTable = localStorage.getItem("activeTable");
    if (activeTable && activeTable !== "0") {
      const table0 = document.getElementById("table0");
      const savedTable = document.getElementById("table" + activeTable);

      if (table0 && savedTable) {
        table0.classList.remove("active");
        savedTable.classList.add("active");

        const menuItems = document.querySelectorAll(".menu-item");
        const itemIndex = parseInt(activeTable) - 1;
        if (menuItems[itemIndex]) {
          menuItems[itemIndex].classList.add("active");
        }
      }
    }
  }

  // Close menu on initial load
  closeMenu();

  // MARK: Registro de Faltas
  // Manejar clic en botón "Registrar Falta"
  const botonesRegistrarFalta = document.querySelectorAll(
    ".btn-registrar-falta"
  );
  if (botonesRegistrarFalta.length > 0) {
    botonesRegistrarFalta.forEach((btn) => {
      btn.addEventListener("click", function () {
        const estudianteId = this.dataset.estudianteId;
        const estudianteNombre = this.dataset.estudianteNombre;
        const cursoId = this.dataset.cursoId;
        const cursoNombre = this.dataset.cursoNombre;

        // Llenar los datos en el modal
        document.getElementById("falta_id_estudiante").value = estudianteId;
        document.getElementById("falta_id_curso").value = cursoId;
        document.getElementById("falta_estudiante_nombre").textContent =
          estudianteNombre;
        document.getElementById("falta_curso_nombre").textContent = cursoNombre;

        // Mostrar el modal
        const modalElement = document.getElementById("modalRegistrarFalta");
        if (modalElement) {
          const modal = new bootstrap.Modal(modalElement);
          modal.show();
        }
      });
    });
  }

  // Limpiar formulario al cerrar el modal de registro de faltas
  const modalRegistrarFalta = document.getElementById("modalRegistrarFalta");
  if (modalRegistrarFalta) {
    modalRegistrarFalta.addEventListener("hidden.bs.modal", function () {
      const form = this.querySelector("form");
      if (form) {
        form.reset();
        // Restaurar valores por defecto con fechas actuales
        const fechaInput = document.getElementById("fecha_registro");
        const horaInput = document.getElementById("hora_registro");

        if (fechaInput) {
          const today = new Date();
          const year = today.getFullYear();
          const month = String(today.getMonth() + 1).padStart(2, "0");
          const day = String(today.getDate()).padStart(2, "0");
          fechaInput.value = `${year}-${month}-${day}`;
        }

        if (horaInput) {
          const now = new Date();
          const hours = String(now.getHours()).padStart(2, "0");
          const minutes = String(now.getMinutes()).padStart(2, "0");
          horaInput.value = `${hours}:${minutes}`;
        }
      }
    });
  }
});
