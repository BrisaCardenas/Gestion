<?php
include 'db.php'; 
include 'sidebar.php'; 

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['usuario_id']) && isset($_POST['equipo_id'])) {
        $usuario_id = $_POST['usuario_id'];
        $equipo_id = $_POST['equipo_id'];
        $fecha_entrega = date('Y-m-d H:i:s');

        $stmtCheck = $conn->prepare("SELECT * FROM asignaciones WHERE equipo_id_Equipo = ?");
        $stmtCheck->bind_param("i", $equipo_id);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $message = "Error: El equipo ya está asignado a otro usuario.";
        } else {
            if (!empty($usuario_id) && !empty($equipo_id)) {
                $stmt = $conn->prepare("INSERT INTO asignaciones (usuario_id_Usuario, equipo_id_Equipo, Fecha_entrega) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $usuario_id, $equipo_id, $fecha_entrega);

                if ($stmt->execute()) {
                    $message = "Asignación creada correctamente.";
                } else {
                    $message = "Error al crear la asignación: " . $stmt->error;
                } 

                $stmt->close();
            } else {
                $message = "Por favor, complete todos los campos.";
            }
        }

        $stmtCheck->close();
    }

    if (isset($_POST['terminate_id'])) {
        $terminate_id = $_POST['terminate_id'];
        $fecha_termino = date('Y-m-d H:i:s');

        $stmtUpdate = $conn->prepare("UPDATE asignaciones SET Fecha_devolucion = ? WHERE id_Asignaciones = ?");
        $stmtUpdate->bind_param("si", $fecha_termino, $terminate_id);

        if ($stmtUpdate->execute()) {
            $message = "Asignación terminada correctamente.";
            $stmtHistorial = $conn->prepare("INSERT INTO historial (usuario_id_Usuario, equipo_id_Equipo, Fecha_devolucion) 
                                              SELECT usuario_id_Usuario, equipo_id_Equipo, ? FROM asignaciones WHERE id_Asignaciones = ?");
            $stmtHistorial->bind_param("si", $fecha_termino, $terminate_id);
            $stmtHistorial->execute();
            $stmtHistorial->close();
        } else {
            $message = "Error al terminar la asignación: " . $stmtUpdate->error;
        }

        $stmtUpdate->close();
    }
}

$resultUsuarios = $conn->query("SELECT id_Usuario, Nombre FROM usuario ORDER BY Nombre ASC"); 
$resultEquipos = $conn->query("SELECT id_Equipo, Nombre FROM equipo WHERE Estado IN ('nuevo', 'Buen estado') ORDER BY Nombre ASC"); 
$resultAsignaciones = $conn->query("SELECT a.id_Asignaciones, u.Nombre AS Usuario, e.Nombre AS Equipo, a.Fecha_entrega 
                                      FROM asignaciones a 
                                      JOIN usuario u ON a.usuario_id_Usuario = u.id_Usuario 
                                      JOIN equipo e ON a.equipo_id_Equipo = e.id_Equipo 
                                      ORDER BY a.Fecha_entrega DESC"); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaciones</title>
    <link rel="stylesheet" href="style2.css"> 
</head>
<body>
<div class="main-container">
    <div class="edit-form">
        <?php if (!empty($message)): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
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
            
            <button type="submit" class="btn-update">Agregar Asignación</button>
        </form>
    </div>
    
    <div class="user-table">
        <h1>Asignaciones</h1>
        <ul>
            <?php
            if ($resultAsignaciones && $resultAsignaciones->num_rows > 0) {
                while ($row = $resultAsignaciones->fetch_assoc()) {
                    echo "<li>
                            <strong>" . htmlspecialchars($row['Usuario']) . "</strong> - " . htmlspecialchars($row['Equipo']) . " - " . htmlspecialchars($row['Fecha_entrega']) . "
                            <form action='' method='POST' style='display:inline;'>
                                <input type='hidden' name='terminate_id' value='" . $row['id_Asignaciones'] . "'>
                                <button type='submit' onclick='return confirm(\"¿Estás seguro de que deseas terminar esta asignación?\");' class='btn-delete'>Terminar Asignación</button>
                            </form>
                          </li>";
                }
            } else {
                echo "<li>No hay asignaciones registradas.</li>";
            }
            ?>
        </ul>
    </div>
</div>
</body>
</html>
