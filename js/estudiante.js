document.addEventListener('DOMContentLoaded', function() {
    // Variables para elementos del menú
    const hamburger = document.querySelector('.hamburger');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');
    const content = document.querySelector('.content');

    // Función para mostrar/ocultar el menú en móviles
    function toggleMenu() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }

    // Event listeners para el menú móvil
    hamburger.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);

    // Comprobar si es una nueva sesión
    if (localStorage.getItem('isNewSession') === 'true') {
        // Mostrar mensaje de bienvenida o realizar acciones iniciales
        console.log('Nueva sesión iniciada');
        localStorage.setItem('isNewSession', 'false');
    }
});

// Función para mostrar diferentes secciones
function showTable(tableNumber) {
    // Ocultar todas las secciones
    const sections = document.querySelectorAll('.table-section');
    sections.forEach(section => {
        section.classList.remove('active');
    });

    // Mostrar la sección seleccionada
    const selectedSection = document.getElementById('table' + tableNumber);
    if (selectedSection) {
        selectedSection.classList.add('active');
    }

    // En móviles, cerrar el menú después de seleccionar una opción
    if (window.innerWidth <= 768) {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.overlay');
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}