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

// MARK: Ver Detalle de Falta Estudiante
// Manejar clic en botón "Ver Detalle"
document.addEventListener('DOMContentLoaded', function() {
    const botonesVerDetalleFaltaEstudiante = document.querySelectorAll(
        ".btn-ver-detalle-falta-estudiante"
    );
    
    if (botonesVerDetalleFaltaEstudiante.length > 0) {
        botonesVerDetalleFaltaEstudiante.forEach((btn) => {
            btn.addEventListener("click", function () {
                const registroId = this.dataset.registroId;

                // Mostrar spinner de carga
                const contenidoDetalle = document.getElementById(
                    "contenido_detalle_falta_estudiante"
                );
                
                if (contenidoDetalle) {
                    contenidoDetalle.innerHTML = `
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando detalle de la falta...</p>
                        </div>
                    `;

                    // Mostrar el modal
                    const modalElement = document.getElementById(
                        "modalDetalleFaltaEstudiante"
                    );
                    
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();

                        // Cargar el detalle mediante AJAX
                        fetch(`obtener_detalle_falta.php?id_registro=${registroId}`)
                            .then((response) => {
                                if (!response.ok) {
                                    throw new Error("Error en la respuesta del servidor");
                                }
                                return response.text();
                            })
                            .then((html) => {
                                contenidoDetalle.innerHTML = html;
                                
                                // Inicializar event listeners después de cargar el contenido
                                inicializarEdicionCompromisos();
                            })
                            .catch((error) => {
                                console.error("Error al cargar detalle:", error);
                                contenidoDetalle.innerHTML = `
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Error al cargar el detalle. Por favor, intente nuevamente.
                                    </div>
                                `;
                            });
                    }
                }
            });
        });
    }
});

// Función para manejar la edición de compromisos
function inicializarEdicionCompromisos() {
    const btnEditar = document.getElementById('btnEditarCompromisos');
    const btnCancelar = document.getElementById('btnCancelarCompromisos');
    const formCompromisos = document.getElementById('form-compromisos');
    const compromisosReadonly = document.getElementById('compromisos-readonly');
    const mensajeDiv = document.getElementById('mensaje-compromisos');
    
    if (!btnEditar || !formCompromisos) return;
    
    // Mostrar formulario de edición
    btnEditar.addEventListener('click', function() {
        compromisosReadonly.style.display = 'none';
        formCompromisos.style.display = 'block';
        btnEditar.style.display = 'none';
    });
    
    // Cancelar edición
    if (btnCancelar) {
        btnCancelar.addEventListener('click', function() {
            compromisosReadonly.style.display = 'block';
            formCompromisos.style.display = 'none';
            btnEditar.style.display = 'inline-block';
            mensajeDiv.innerHTML = '';
        });
    }
    
    // Enviar formulario con AJAX
    formCompromisos.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Mostrar mensaje de carga
        mensajeDiv.innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-hourglass-split"></i> Guardando compromisos...
            </div>
        `;
        
        fetch('actualizar_compromisos.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar la vista de solo lectura
                if (data.compromisos && data.compromisos.trim() !== '') {
                    compromisosReadonly.innerHTML = data.compromisos.replace(/\n/g, '<br>');
                } else {
                    compromisosReadonly.innerHTML = '<em class="text-muted">No hay compromisos registrados aún. Haz clic en "Editar" para agregar tus compromisos.</em>';
                }
                
                // Mostrar mensaje de éxito
                mensajeDiv.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> ${data.mensaje}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                // Volver a vista de solo lectura después de 2 segundos
                setTimeout(() => {
                    compromisosReadonly.style.display = 'block';
                    formCompromisos.style.display = 'none';
                    btnEditar.style.display = 'inline-block';
                    mensajeDiv.innerHTML = '';
                }, 2000);
            } else {
                mensajeDiv.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle"></i> ${data.mensaje}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mensajeDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> Error al guardar los compromisos
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        });
    });
}