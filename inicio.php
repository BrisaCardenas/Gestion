<?php 
include 'db.php'; 
include 'sidebar.php';

$searchTerm = '';
$searchResults = [];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchTerm = $_POST['searchTerm'];

    
    $sqlEquipos = "SELECT Nombre, Estado FROM equipo WHERE Nombre LIKE ? OR Estado LIKE ?";
    $stmtEquipos = $conn->prepare($sqlEquipos);
    $likeTerm = "%$searchTerm%";
    $stmtEquipos->bind_param("ss", $likeTerm, $likeTerm);
    $stmtEquipos->execute();
    $resultEquipos = $stmtEquipos->get_result();
    
    
    $sqlHistorial = "SELECT u.Nombre AS Usuario, e.Nombre AS Equipo, h.Fecha_devolucion FROM historial h JOIN usuario u ON h.usuario_id_Usuario = u.id_Usuario JOIN equipo e ON h.equipo_id_Equipo = e.id_Equipo WHERE u.Nombre LIKE ? OR e.Nombre LIKE ?";
    $stmtHistorial = $conn->prepare($sqlHistorial);
    $stmtHistorial->bind_param("ss", $likeTerm, $likeTerm);
    $stmtHistorial->execute();
    $resultHistorial = $stmtHistorial->get_result();

   
    $sqlIncidentes = "SELECT i.Descripcion_suceso, u.Nombre AS Usuario, e.Nombre AS Equipo, i.Fecha FROM incidentes i JOIN usuario u ON i.usuario_id_Usuario = u.id_Usuario JOIN equipo e ON i.equipo_id_Equipo = e.id_Equipo WHERE i.Descripcion_suceso LIKE ? OR u.Nombre LIKE ? OR e.Nombre LIKE ?";
    $stmtIncidentes = $conn->prepare($sqlIncidentes);
    $stmtIncidentes->bind_param("sss", $likeTerm, $likeTerm, $likeTerm);
    $stmtIncidentes->execute();
    $resultIncidentes = $stmtIncidentes->get_result();

    
    $searchResults['equipos'] = $resultEquipos;
    $searchResults['historial'] = $resultHistorial;
    $searchResults['incidentes'] = $resultIncidentes;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="inicio.css">
    <link rel="stylesheet" href="style2.css">
</head>
<body> 

<div class="main-container">
    <form action="" method="POST" class="search-form">
        <input type="text" name="searchTerm" placeholder="Buscar..." value="<?php echo htmlspecialchars($searchTerm); ?>" required>
        <button type="submit">Buscar</button>
    </form>
    

    <img src="bu.jpng" alt="Descripción de la imagen" class="search-image">
    
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <h2>Resultados de Equipos</h2>
        <ul>
            <?php 
            if (isset($searchResults['equipos']) && $searchResults['equipos']->num_rows > 0) {
                while ($row = $searchResults['equipos']->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['Nombre']) . " - " . htmlspecialchars($row['Estado']) . "</li>";
                }
            } else {
                echo "<li>No se encontraron equipos.</li>";
            }
            ?>
        </ul>

        <h2>Resultados de Historial</h2>
        <ul>
            <?php
            if (isset($searchResults['historial']) && $searchResults['historial']->num_rows > 0) {
                while ($row = $searchResults['historial']->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['Usuario']) . " - " . htmlspecialchars($row['Equipo']) . " - " . htmlspecialchars($row['Fecha_devolucion']) . "</li>";
                }
            } else {
                echo "<li>No se encontraron registros en el historial.</li>";
            }
            ?>
        </ul>

        <h2>Resultados de Incidentes</h2>
        <ul>
            <?php
            if (isset($searchResults['incidentes']) && $searchResults['incidentes']->num_rows > 0) {
                while ($row = $searchResults['incidentes']->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['Descripcion_suceso']) . " - " . htmlspecialchars($row['Usuario']) . " - " . htmlspecialchars($row['Equipo']) . " - " . htmlspecialchars($row['Fecha']) . "</li>";
                }
            } else {
                echo "<li>No se encontraron incidentes.</li>";
            }
            ?>
        </ul>
    <?php endif; ?>
</div>

</body>
</html>