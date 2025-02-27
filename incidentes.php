<?php
session_start(); 
include 'db.php'; 
include 'sidebar.php'; 

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_SESSION['email'])) {
        $usuario_correo = $_SESSION['email'];
    } else {
        $message = "Error: No se ha iniciado sesión.";
        
        header("Location: index.php");
        exit();
    }

    $equipo_id = $_POST['equipo_id'];
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];

    if (!empty($usuario_correo) && !empty($equipo_id) && !empty($fecha) && !empty($descripcion)) {
        $stmtInsert = $conn->prepare("INSERT INTO incidentes (usuario_Correo, equipo_id_Equipo, Fecha, Descripcion_suceso) VALUES (?, ?, ?, ?)");
        $stmtInsert->bind_param("siss", $usuario_correo, $equipo_id, $fecha, $descripcion);

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
  
    if (isset($_POST['estado']) && !empty($_POST['estado'])) {
        $nuevo_estado = $_POST['estado'];

        $stmtUpdateEstado = $conn->prepare("UPDATE equipo SET Estado = ? WHERE id_Equipo = ?");
        $stmtUpdateEstado->bind_param("si", $nuevo_estado, $equipo_id);

        if ($stmtUpdateEstado->execute()) {
            $message = "Estado del equipo actualizado correctamente.";
        } else {
            $message = "Error al actualizar el estado: " . $stmtUpdateEstado->error;
        }

        $stmtUpdateEstado->close();
    }


$resultUsuarios = $conn->query("SELECT Correo, Nombre FROM usuario ORDER BY Nombre ASC"); 
$resultEquipos = $conn->query("SELECT id_Equipo, Nombre FROM equipo ORDER BY Nombre ASC"); 
$resultIncidentes = $conn->query("SELECT i.id_Incidentes, u.Nombre AS Usuario, e.Nombre AS Equipo, i.Fecha, i.Descripcion_suceso 
                                    FROM incidentes i 
                                    JOIN usuario u ON i.usuario_Correo = u.Correo 
                                    JOIN equipo e ON i.equipo_id_Equipo = e.id_Equipo 
                                    ORDER BY i.Fecha DESC"); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidentes</title>
    <link rel="stylesheet" href="incidente.css"> 
    <link rel="stylesheet" href="style2.css"> 
    <script>
        function confirmChange() {
            const estadoSelect = document.getElementById('estado');
            const selectedValue = estadoSelect.value;

            if (selectedValue) {
                const confirmMessage = `¿Estás seguro de que deseas cambiar el estado a "${selectedValue}"?`;
                if (!confirm(confirmMessage)) {
                    estadoSelect.value = "";
                }
            }
        }
    </script>
</head>
<body>
<div class="main-container">
    <div class="edit-form">
        <?php if (!empty($message)): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <form action="" method="POST"> 
            <label for="usuario_correo">Usuario:</label>
            <select id="usuario_correo" name="usuario_correo" required>
                <option value="">Seleccione un usuario</option>
                <?php while ($row = $resultUsuarios->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['Correo']); ?>"><?php echo htmlspecialchars($row['Nombre']); ?></option>
                <?php endwhile; ?>
            </select>
            
            <label for="equipo_id">Equipo:</label>
            <select id="equipo_id" name="equipo_id" required>
                <option value="">Seleccione un equipo</option>
                <?php while ($row = $resultEquipos->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id_Equipo']); ?>"><?php echo htmlspecialchars($row['Nombre']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" required style="width: 50%;">

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required style="width: 100%;"></textarea>

            <label for="estado">Nuevo estado del equipo:</label>
            <select id="estado" name="estado" onchange="confirmChange()" style="width: 50%;">
                <option value="">Seleccione un estado (opcional)</option>
                <option value="nuevo">Nuevo</option>
                <option value="usado">Usado</option>
                <option value="dañado">Dañado</option>
                <option value="en reparacion">En reparación</option>
            </select>
            
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