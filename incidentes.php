<?php
include 'db.php'; 
include 'sidebar.php'; 

// Inicializar variable de mensaje
$message = "";

// Manejo de inserción de datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_POST['usuario_id'];
    $equipo_id = $_POST['equipo_id'];
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];

    if (!empty($usuario_id) && !empty($equipo_id) && !empty($fecha) && !empty($descripcion)) {
        // Insertar nuevo incidente
        $sqlInsert = "INSERT INTO incidentes (usuario_id_Usuario, equipo_id_Equipo, Fecha, Descripcion_suceso) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("iiss", $usuario_id, $equipo_id, $fecha, $descripcion);

        if ($stmtInsert->execute()) {
            $message = "Incidente agregado correctamente.";
        } else {
            $message = "Error al agregar el incidente: " . $stmtInsert->error;
        }

        $stmtInsert->close();
    } else {
        $message = "Por favor, complete todos los campos.";
    }
}

// Manejo de eliminación de asignación
if (isset($_POST['terminate_id'])) {
    $terminate_id = $_POST['terminate_id'];
    $fecha_termino = date('Y-m-d H:i:s');

    // Actualizar la asignación a terminada
    $sqlUpdate = "UPDATE asignaciones SET Fecha_devolucion = ? WHERE id_Asignaciones = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $fecha_termino, $terminate_id);

    if ($stmtUpdate->execute()) {
        $message = "Asignación terminada correctamente.";
    } else {
        $message = "Error al terminar la asignación: " . $stmtUpdate->error;
    }

    $stmtUpdate->close();
}

// Obtener usuarios y equipos para los menús desplegables
$sqlUsuarios = "SELECT id_Usuario, Nombre FROM usuario ORDER BY Nombre ASC"; 
$resultUsuarios = $conn->query($sqlUsuarios);

$sqlEquipos = "SELECT id_Equipo, Nombre FROM equipo ORDER BY Nombre ASC"; 
$resultEquipos = $conn->query($sqlEquipos);

$sqlIncidentes = "SELECT i.id_Incidentes, u.Nombre AS Usuario, e.Nombre AS Equipo, i.Fecha, i.Descripcion_suceso 
                  FROM incidentes i 
                  JOIN usuario u ON i.usuario_id_Usuario = u.id_Usuario 
                  JOIN equipo e ON i.equipo_id_Equipo = e.id_Equipo 
                  ORDER BY i.Fecha DESC"; 
$resultIncidentes = $conn->query($sqlIncidentes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidentes</title>
    <link rel="stylesheet" href="incidente.css"> 
    <link rel="stylesheet" href="style2.css"> 
</head>
<body>
<div class="main-container">
    <div class="edit-form">
        <?php if (!empty($message)): ?>
            <div class="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="POST"> 
            <label for="usuario_id">Usuario:</label>
            <select id="usuario_id" name="usuario_id" required>
                <option value="">Seleccione un usuario</option>
                <?php while ($row = $resultUsuarios->fetch_assoc()): ?>
                    <option value="<?php echo $row['id_Usuario']; ?>"><?php echo htmlspecialchars($row['Nombre']); ?></option>
                <?php endwhile; ?>
            </select>
            
            <label for="equipo_id">Equipo:</label>
            <select id="equipo_id" name="equipo_id" required>
                <option value="">Seleccione un equipo</option>
                <?php while ($row = $resultEquipos->fetch_assoc()): ?>
                    <option value="<?php echo $row['id_Equipo']; ?>"><?php echo htmlspecialchars($row['Nombre']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" required style="width: 50%;">

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required style="width: 100%;"></textarea>
            
            <button type="submit" class="btn-update">Agregar Incidente</button>
        </form>
    </div>
    
    <div class="user-table">
        <h1>Incidentes</h1>
        <ul>
            <?php
            if ($resultIncidentes && $resultIncidentes->num_rows > 0) {
                while ($row = $resultIncidentes->fetch_assoc()) {
                    echo "<li>
                            <strong>" . htmlspecialchars($row['Descripcion_suceso']) . "</strong> - " . htmlspecialchars($row['Usuario']) . " - " . htmlspecialchars($row['Equipo']) . " - " . htmlspecialchars($row['Fecha']) . "
                            <form action='' method='POST' style='display:inline;'>
                                <input type='hidden' name='terminate_id' value='" . $row['id_Incidentes'] . "'>
                                <button type='submit' onclick='return confirm(\"¿Estás seguro de que deseas terminar esta asignación?\");' class='btn-delete'>Terminar Asignación</button>
                            </form>
                          </li>";
                }
            } else {
                echo "<li>No hay incidentes registrados.</li>";
            }
            ?>
        </ul>
    </div>
</div>
</body>
</html>