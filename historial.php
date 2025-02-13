<?php
include 'db.php'; 
include 'sidebar.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial</title>
    <link rel="stylesheet" href="style2.css"> 
</head>
<body>
<div class="main-container">
    <div class="user-table">
        <h1>Historial de Usuarios</h1>
        <ul>
            <?php
            $sqlUsuarios = "SELECT h.id_historial, u.Nombre AS Usuario, e.Nombre AS Equipo, h.Fecha_devolucion 
                            FROM historial h 
                            JOIN usuario u ON h.usuario_id_Usuario = u.id_Usuario 
                            JOIN equipo e ON h.equipo_id_Equipo = e.id_Equipo 
                            ORDER BY h.Fecha_devolucion DESC";  
            $resultUsuarios = $conn->query($sqlUsuarios);
            if ($resultUsuarios && $resultUsuarios->num_rows > 0) {
                while ($row = $resultUsuarios->fetch_assoc()) {
                    echo "<li>
                            <strong>" . htmlspecialchars($row['Usuario']) . "</strong> - " . htmlspecialchars($row['Equipo']) . " - " . htmlspecialchars($row['Fecha_devolucion']) . "
                          </li>";
                }
            } else {
                echo "<li>No hay registros en el historial de usuarios.</li>";
            }
            ?>
        </ul>
    </div>

    <div class="user-table">
        <h1>Historial de Equipos</h1>
        <ul>
            <?php
            $sqlEquipos = "SELECT e.Nombre AS Equipo, e.Estado, u.Nombre AS Usuario, h.Fecha_devolucion 
                            FROM historial h 
                            JOIN equipo e ON h.equipo_id_Equipo = e.id_Equipo 
                            LEFT JOIN usuario u ON h.usuario_id_Usuario = u.id_Usuario 
                            ORDER BY h.Fecha_devolucion DESC";  
            $resultEquipos = $conn->query($sqlEquipos);
            if ($resultEquipos && $resultEquipos->num_rows > 0) {
                while ($row = $resultEquipos->fetch_assoc()) {
                    echo "<li>
                            <strong>" . htmlspecialchars($row['Equipo']) . "</strong> - Estado: " . htmlspecialchars($row['Estado']) . " - Asignado a: " . (htmlspecialchars($row['Usuario']) ?: 'Nadie') . " - " . htmlspecialchars($row['Fecha_devolucion']) . "
                          </li>";
                }
            } else {
                echo "<li>No hay registros en el historial de equipos.</li>";
            }
            ?>
        </ul>
    </div>
</div>
</body>
</html>