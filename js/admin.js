// Variable para detectar si es una nueva sesión
let isNewSession = typeof sessionStarted !== 'undefined' ? !sessionStarted : true;

// Detectar si se hizo un POST (submit de formulario)
let isFormSubmit = typeof isPost !== 'undefined' ? isPost : false;

// Guardar la tabla activa en localStorage
function saveActiveTable(tableNumber) {
    localStorage.setItem('activeTableAdmin', tableNumber);
}

// Restaurar la tabla activa INMEDIATAMENTE (antes de que se renderice)
(function() {
    // Si es una nueva sesión Y NO es un submit de formulario, mostrar el mensaje de bienvenida
    if (isNewSession && !isFormSubmit) {
        localStorage.setItem('activeTableAdmin', '0');
        return;
    }
    
    const activeTable = localStorage.getItem('activeTableAdmin');
    // Si no hay tabla guardada, no hacer nada (mostrar el mensaje de bienvenida)
    if (activeTable && activeTable !== '0') {
        // Ocultar table0 y mostrar la tabla guardada ANTES de que se vea
        const table0 = document.getElementById('table0');
        const savedTable = document.getElementById('table' + activeTable);
        
        if (table0 && savedTable) {
            table0.classList.remove('active');
            savedTable.classList.add('active');
        }
    }
})();

function toggleMenu() {
    const hamburger = document.querySelector('.hamburger');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');
    
    hamburger.classList.toggle('active');
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
}

// Cerrar el menú si está abierto
function closeMenu() {
    const hamburger = document.querySelector('.hamburger');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');
    
    hamburger.classList.remove('active');
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
}

function showTable(tableNumber, skipMenuClose = false) {
    // Cerrar menú al inicio
    closeMenu();
    
    const sections = document.querySelectorAll('.table-section');
    sections.forEach(section => section.classList.remove('active'));
    
    const selectedSection = document.getElementById('table' + tableNumber);
    if (selectedSection) {
        selectedSection.classList.add('active');
    }
    
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => item.classList.remove('active'));
    
    // Solo marcar el menú si no es la tabla 0 (inicio)
    if (tableNumber > 0) {
        const itemIndex = tableNumber - 1;
        if (menuItems[itemIndex]) {
            menuItems[itemIndex].classList.add('active');
        }
    }
    
    // Guardar la tabla activa
    saveActiveTable(tableNumber);
}

// Restaurar la tabla activa al cargar completamente la página
window.addEventListener('DOMContentLoaded', function() {
    // Asegurarse de que el menú esté cerrado al cargar
    closeMenu();
    
    // Si no es nueva sesión O es un submit de formulario, verificar y actualizar el menú activo
    if (!isNewSession || isFormSubmit) {
        const activeTable = localStorage.getItem('activeTableAdmin');
        if (activeTable && activeTable !== '0') {
            const menuItems = document.querySelectorAll('.menu-item');
            const itemIndex = parseInt(activeTable) - 1;
            if (menuItems[itemIndex]) {
                menuItems[itemIndex].classList.add('active');
            }
        }
    }
});