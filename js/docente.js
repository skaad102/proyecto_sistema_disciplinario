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
      document.body.classList.toggle('menu-open');
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
});
