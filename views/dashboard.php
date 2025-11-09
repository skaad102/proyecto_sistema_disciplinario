<?php
session_start();
if(!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit();
}

// Verificar el rol del usuario
$esAdmin = isset($_SESSION['usuario']['rol']) && $_SESSION['usuario']['rol'] === 'Directivo';
if (!$esAdmin) {
    header('Location: estudiante/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Sistema de Gestión de Faltas</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.min.css">
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            from { opacity: 0; }
            to { opacity: 1; }
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
            <h2><i class="bi bi-grid-1x2-fill"></i> Panel de Control</h2>
            <p>Sistema de Gestión de Faltas</p>
        </div>
        <ul class="menu-list">
            <li class="menu-item" onclick="showTable(1)">
                <i class="bi bi-book"></i> Asignaturas
            </li>
            <li class="menu-item" onclick="showTable(2)">
                <i class="bi bi-person-vcard"></i> Docentes
            </li>
            <li class="menu-item" onclick="showTable(3)">
                <i class="bi bi-people"></i> Estudiantes
            </li>
            <li class="menu-item" onclick="showTable(4)">
                <i class="bi bi-collection"></i> Cursos
            </li>
            <li class="menu-item" onclick="showTable(5)">
                <i class="bi bi-exclamation-triangle"></i> Faltas
            </li>
            <li class="menu-item" onclick="showTable(6)">
                <i class="bi bi-list-check"></i> Seguimientos
            </li>
            <li class="menu-item" onclick="showTable(7)">
                <i class="bi bi-person-gear"></i> Usuarios
            </li>
            <li class="menu-item" onclick="showTable(8)">
                <i class="bi bi-tags"></i> Tipos de Falta
            </li>
        </ul>
    </div>

    <div class="header">
        <div class="welcome" onclick="showTable(0)" style="cursor: pointer;">
            <i class="bi bi-house-door"></i>
            <span>Bienvenido,</span>
            <strong><?php echo htmlspecialchars($_SESSION['usuario']['nombre'] ?? $_SESSION['usuario']); ?></strong>
        </div>
        <a href="../views/auth/logout.php" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
    </div>

    <div class="content">
        <div class="table-section active" id="table0">
            <div class="welcome-message">
                <div class="welcome-icon">
                    <i class="bi bi-grid-1x2-fill"></i>
                </div>
                <h2>Panel Administrativo</h2>
                <p>Selecciona una opción del menú para gestionar el sistema</p>
            </div>
        </div>

        <!-- Secciones para cada tabla -->
        <div class="table-section" id="table1">
            <?php include '../views/asignaturas/index.php'; ?>
        </div>

        <div class="table-section" id="table2">
            <?php include '../views/docentes/index.php'; ?>
        </div>

        <div class="table-section" id="table3">
            <?php include '../views/estudiantes/index.php'; ?>
        </div>

        <div class="table-section" id="table4">
            <?php include '../views/cursos/index.php'; ?>
        </div>

        <div class="table-section" id="table5">
            <?php include '../views/faltas/index.php'; ?>
        </div>

        <div class="table-section" id="table6">
            <?php include '../views/seguimientos/index.php'; ?>
        </div>

        <div class="table-section" id="table7">
            <?php include '../views/usuarios/index.php'; ?>
        </div>

        <div class="table-section" id="table8">
            <?php include '../views/tipos_falta/index.php'; ?>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        let isNewSession = <?php echo !isset($_SESSION['session_started']) ? 'true' : 'false'; ?>;
        let isFormSubmit = <?php echo ($_SERVER['REQUEST_METHOD'] === 'POST') ? 'true' : 'false'; ?>;
        
        <?php 
        if(!isset($_SESSION['session_started'])){
            $_SESSION['session_started'] = true;
        }
        ?>

        function saveActiveTable(tableNumber) {
            localStorage.setItem('activeTable', tableNumber);
        }

        // Restauración inmediata de la tabla activa
        (function() {
            if (isNewSession && !isFormSubmit) {
                localStorage.setItem('activeTable', '0');
                return;
            }
            
            const activeTable = localStorage.getItem('activeTable');
            if (activeTable && activeTable !== '0') {
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

        function closeMenu() {
            const hamburger = document.querySelector('.hamburger');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.overlay');
            
            hamburger.classList.remove('active');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }

        function showTable(tableNumber) {
            closeMenu();
            
            const sections = document.querySelectorAll('.table-section');
            sections.forEach(section => section.classList.remove('active'));
            
            const selectedSection = document.getElementById('table' + tableNumber);
            if (selectedSection) {
                selectedSection.classList.add('active');
            }
            
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => item.classList.remove('active'));
            
            if (tableNumber > 0) {
                const itemIndex = tableNumber - 1;
                if (menuItems[itemIndex]) {
                    menuItems[itemIndex].classList.add('active');
                }
            }
            
            saveActiveTable(tableNumber);
        }

        window.addEventListener('DOMContentLoaded', function() {
            closeMenu();
            
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