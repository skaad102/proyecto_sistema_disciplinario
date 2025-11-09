<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            min-height: 100vh;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 0.8rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .welcome {
            font-size: 1.5rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: 4rem;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 0.6rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            font-weight: 500;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .hamburger {
            position: fixed;
            top: 0.7rem;
            left: 1rem;
            width: 35px;
            height: 30px;
            cursor: pointer;
            z-index: 1001;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hamburger span {
            width: 100%;
            height: 4px;
            background: #333;
            border-radius: 2px;
            transition: all 0.3s;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(10px, 10px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(8px, -8px);
        }

        .sidebar {
            position: fixed;
            left: -300px;
            top: 0;
            width: 280px;
            height: 100vh;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: left 0.3s;
            z-index: 999;
            overflow-y: auto;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            background: white;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }

        .sidebar-header h2 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .menu-list {
            list-style: none;
            padding: 1rem 0;
        }

        .menu-item {
            padding: 1rem 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            color: #333;
            font-weight: 500;
        }

        .menu-item:hover {
            background: #f8f9fa;
            border-left-color: #667eea;
            color: #667eea;
        }

        .menu-item.active {
            background: #f8f9fa;
            border-left-color: #667eea;
            color: #667eea;
        }

        .content {
            margin-left: 0;
            margin-top: 70px;
            padding: 2rem;
            transition: margin-left 0.3s;
            display: flex;
            justify-content: center;
            opacity: 1;
        }

        .table-section {
            display: none;
            width: 100%;
            max-width: 1200px;
            animation: fadeIn 0.2s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .table-section.active {
            display: block;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            z-index: 998;
        }

        .overlay.active {
            display: block;
        }

        .welcome-message {
            text-align: center;
            padding: 4rem 2rem;
        }

        .welcome-message h2 {
            color: #667eea;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .welcome-message p {
            color: #7f8c8d;
            font-size: 1.2rem;
        }

        .welcome-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }

            .welcome {
                font-size: 1.2rem;
            }

            .hamburger {
                left: 1rem;
            }

            .content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="hamburger" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="overlay" onclick="toggleMenu()"></div>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>📊 Menú de Tablas</h2>
            <p>Selecciona una tabla</p>
        </div>
        <ul class="menu-list">
            <li class="menu-item" onclick="showTable(1)">📋 Asignaturas</li>
            <li class="menu-item" onclick="showTable(2)">📋 Estudiantes</li>
            <li class="menu-item" onclick="showTable(3)">📋 Tabla3</li>
            <li class="menu-item" onclick="showTable(4)">📋 Tabla4</li>
            <li class="menu-item" onclick="showTable(5)">📋 Tabla5</li>
            <li class="menu-item" onclick="showTable(6)">📋 Tabla6</li>
            <li class="menu-item" onclick="showTable(7)">📋 Tabla7</li>
            <li class="menu-item" onclick="showTable(8)">📋 Tabla8</li>
            <li class="menu-item" onclick="showTable(9)">📋 Tabla9</li>
            <li class="menu-item" onclick="showTable(10)">📋 Tabla10</li>
            <li class="menu-item" onclick="showTable(11)">📋 Tabla11</li>
            <li class="menu-item" onclick="showTable(12)">📋 Tabla12</li>
            <li class="menu-item" onclick="showTable(13)">📋 Tabla13</li>
        </ul>
    </div>

    <div class="header">
        <div class="welcome" onclick="showTable(0)" style="cursor: pointer;">
            <span> Bienvenido </span>
            <strong><?php echo $_SESSION['usuario']; ?></strong>
        </div>
        <a href="cerrar_sesion.php" class="logout-btn">🚪 Cerrar Sesión</a>
    </div>

    <div class="content">
        <div class="table-section active" id="table0">
            <div class="welcome-message">
                <div class="welcome-icon">📊</div>
                <h2>Bienvenido al Panel Administrativo</h2>
                <p>Selecciona una tabla del menú para comenzar</p>
            </div>
        </div>

        <div class="table-section" id="table1">
            <?php include 'asignatura_linda.php'; ?>
        </div>

        <div class="table-section" id="table2">
            <?php include 'estudiante_natalia.php'; ?>
        </div>

        <div class="table-section" id="table3">
            <?php include 'curso_mariana.php'; ?>
        </div>

        <div class="table-section" id="table4">
            <?php include 'tabla4.php'; ?>
        </div>

        <div class="table-section" id="table5">
            <?php include 'tabla5.php'; ?>
        </div>

        <div class="table-section" id="table6">
            <?php include 'tabla6.php'; ?>
        </div>

        <div class="table-section" id="table7">
            <?php include 'tabla7.php'; ?>
        </div>

        <div class="table-section" id="table8">
            <?php include 'tabla8.php'; ?>
        </div>

        <div class="table-section" id="table9">
            <?php include 'tabla9.php'; ?>
        </div>

        <div class="table-section" id="table10">
            <?php include 'tabla10.php'; ?>
        </div>

        <div class="table-section" id="table11">
            <?php include 'tabla11.php'; ?>
        </div>

        <div class="table-section" id="table12">
            <?php include 'tabla12.php'; ?>
        </div>

        <div class="table-section" id="table13">
            <?php include 'tabla13.php'; ?>
        </div>
    </div>

    <script>
        // Variable para detectar si es una nueva sesión
        let isNewSession = <?php echo !isset($_SESSION['session_started']) ? 'true' : 'false'; ?>;
        
        // Detectar si se hizo un POST (submit de formulario)
        let isFormSubmit = <?php echo ($_SERVER['REQUEST_METHOD'] === 'POST') ? 'true' : 'false'; ?>;
        
        <?php 
        // Marcar que la sesión ya está iniciada
        if(!isset($_SESSION['session_started'])){
            $_SESSION['session_started'] = true;
        }
        ?>

        // Guardar la tabla activa en localStorage
        function saveActiveTable(tableNumber) {
            localStorage.setItem('activeTable', tableNumber);
        }

        // Restaurar la tabla activa INMEDIATAMENTE (antes de que se renderice)
        (function() {
            // Si es una nueva sesión Y NO es un submit de formulario, mostrar el mensaje de bienvenida
            if (isNewSession && !isFormSubmit) {
                localStorage.setItem('activeTable', '0');
                return;
            }
            
            const activeTable = localStorage.getItem('activeTable');
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
                const activeTable = localStorage.getItem('activeTable');
                if (activeTable && activeTable !== '0') {
                    const menuItems = document.querySelectorAll('.menu-item');
                    const itemIndex = parseInt(activeTable) - 1;
                    if (menuItems[itemIndex]) {
                        menuItems[itemIndex].classList.add('active');
                    }
                }
            }
        });
    </script>
</body>
</html>