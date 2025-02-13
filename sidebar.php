<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css"> 
    <script src="sidebar.js" defer></script> 
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
        <div class="menu-item" onclick="location.href='inicio.php';">
            <span class="menu-text">🏠 Inicio</span>
        </div>
        <div class="menu-item" onclick="location.href='usuarios.php';">
            <span class="menu-text">👥 Personas JX</span>
        </div>
        <div class="menu-item" onclick="location.href='equipos.php';">
            <span class="menu-text">💻 Equipos</span>
        </div>
        <div class="menu-item" onclick="location.href='asignaciones.php';">
            <span class="menu-text">✔ Asignaciones</span>
        </div>
        <div class="menu-item" onclick="location.href='incidentes.php';">
            <span class="menu-text">🔨 Incidentes</span>
        </div>
        <div class="menu-item" onclick="location.href='historial.php';">
            <span class="menu-text">📕 Historial</span>
        </div>
    </div>

    <div class="main-content">
        <!-- Contenido principal aquí -->
    </div>
    <button class="toggle-button" onclick="toggleSidebar()">☰</button> 
</body>
</html>