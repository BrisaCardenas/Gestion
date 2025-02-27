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
    <link rel="stylesheet" href="historial.css">
</head>
<body>
<div class="main-container">
    <div class="user-table">
        <h1>Historial</h1>
        <ul>
            <?php
            // Consulta para obtener el historial de acciones (inserciones y modificaciones)
            $sqlHistorial = "SELECT * FROM historial ORDER BY fecha DESC";
            $resultHistorial = $conn->query($sqlHistorial);
            
            if ($resultHistorial && $resultHistorial->num_rows > 0) {
                while ($row = $resultHistorial->fetch_assoc()) {
                    echo "<li>
                            <strong>Tabla:</strong> " . htmlspecialchars($row['tabla']) . " - 
                            <strong>Acción:</strong> " . htmlspecialchars($row['accion']) . " - 
                            <strong>Datos:</strong> " . htmlspecialchars($row['datos']) . " - 
                            <strong>Tipo de Acción:</strong> " . htmlspecialchars($row['Tipo_Accion']) . " - 
                            <strong>Usuario:</strong> " . htmlspecialchars($row['usuario_Correo']) . " - 
                            <strong>Fecha:</strong> " . htmlspecialchars($row['fecha']) . "
                          </li>";
                }
            } else {
                echo "<li>No hay registros en el historial.</li>";
            }
            ?>
        </ul>
    </div>
</div>
</body>
</html>