<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css"> 
    <link rel="icon" href="fotojx.png" type="image/x-icon"> 
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="fotojx2.png" alt="Logo" style="width: 30px; height: 30px; object-fit: cover;">
            </div>
            <span class="title">JX Gestion</span>
        </div>
        <div class="menu-item" onclick="location.href='inicio.php';" >
            <img src="hogar.png" alt="Inicio" style="width: 20px; height: 20px; margin-right: 8px;"> 
            <span class="menu-text">Inicio</span>
        </div>
        <div class="menu-item" onclick="location.href='usuarios.php';" >
            <img src="equipo.png" alt="Usuarios" style="width: 20px; height: 20px; margin-right: 8px;"> 
            <span class="menu-text">Personas JX</span>
        </div>
        <div class="menu-item" onclick="location.href='equipos.php';" >
            <img src="computadora.png" alt="Equipos" style="width: 20px; height: 20px; margin-right: 8px;"> 
            <span class="menu-text">Equipos</span>
        </div>
        <div class="menu-item" onclick="location.href='asignaciones.php';" >
            <img src="lista.png" alt="Asignaciones" style="width: 20px; height: 20px; margin-right: 8px;"> 
            <span class="menu-text">Asignaciones</span>
        </div>
        <div class="menu-item" onclick="location.href='incidentes.php';" >
            <img src="herramienta-de-desarrollo.png" alt="Incidentes" style="width: 20px; height: 20px; margin-right: 8px;"> 
            <span class="menu-text">Incidentes</span>
        </div>
        <div class="menu-item" onclick="location.href='historial.php';" >
            <img src="nube.png" alt="Historial" style="width: 20px; height: 20px; margin-right: 8px;"> 
            <span class="menu-text">Historial</span>
        </div>
    </div>

    <div class="main-content">
        <!-- Contenido principal aquí -->
    </div>
    <button class="toggle-button" onclick="toggleSidebar()">☰</button>
    <script src="sidebar.js"></script> 
</body>
</html>