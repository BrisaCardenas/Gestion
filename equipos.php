<?php
include 'db.php'; 
include 'sidebar.php'; 

$message = "";
$nombre = ""; 
$tipo_equipo = ""; 
$estado = ""; 
$descripcion = ""; 
$id_equipo = "";

//Agregar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nombre = $_POST['nombre'];
    $tipo_equipo = $_POST['tipo_equipo'];
    $estado = $_POST['estado'];
    $descripcion = $_POST['descripcion'];

    if (!empty($nombre) && !empty($tipo_equipo) && !empty($estado) && !empty($descripcion)) {
        $sql = "INSERT INTO equipo (Nombre, Tipo_Equipo, Estado, Descripcion) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $tipo_equipo, $estado, $descripcion);

        if ($stmt->execute()) {
            $message = "Equipo agregado correctamente."; 
        } else {
            $message = "Error al agregar el equipo: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Por favor, complete todos los campos.";
    }
}

//Modificar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id_equipo = $_POST['id_equipo'];
    $nombre = $_POST['nombre'];
    $tipo_equipo = $_POST['tipo_equipo'];
    $estado = $_POST['estado'];
    $descripcion = $_POST['descripcion'];

    if (!empty($nombre) && !empty($tipo_equipo) && !empty($estado) && !empty($descripcion)) {
        $sql = "UPDATE equipo SET Nombre=?, Tipo_Equipo=?, Estado=?, Descripcion=? WHERE id_Equipo=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $tipo_equipo, $estado, $descripcion, $id_equipo);

        if ($stmt->execute()) {
            $message = "Equipo modificado correctamente."; 
        } else {
            $message = "Error al modificar el equipo: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Por favor, complete todos los campos.";
    }
}

//Eliminar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id_equipo = $_POST['id_equipo'];

    
    $stmtCheck = $conn->prepare("SELECT * FROM asignaciones WHERE equipo_id_Equipo = ?");
    $stmtCheck->bind_param("i", $id_equipo);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        
        $message = "Error: No se puede eliminar el equipo porque tiene asignaciones relacionadas.";
    } else {
        $sql = "DELETE FROM equipo WHERE id_Equipo=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_equipo);

        if ($stmt->execute()) {
            $message = "Equipo eliminado correctamente."; 
        } else {
            $message = "Error al eliminar el equipo: " . $stmt->error;
        }

        $stmt->close();
    }
    $stmtCheck->close();
}

$sql = "SELECT id_Equipo, Nombre, Tipo_Equipo, Estado, Descripcion FROM equipo ORDER BY Nombre ASC"; 
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos</title>
    <link rel="stylesheet" href="equipos.css"> 
    <link rel="stylesheet" href="style2.css"> 
    <script>
        function openEditModal(id, nombre, tipo_equipo, estado, descripcion) {
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('edit_id_equipo').value = id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_tipo_equipo').value = tipo_equipo;
            document.getElementById('edit_estado').value = estado;
            document.getElementById('edit_descripcion').value = descripcion;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDelete(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este equipo?")) {
                document.getElementById('delete_id_equipo').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</head>
<body>
<div class="main-content">
    <div class="main-container">
        <div class="edit-form">
            <?php if (!empty($message)): ?>
                <div class="alert">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <input type="hidden" name="action" value="add">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                
                <label for="tipo_equipo">Tipo de Equipo:</label>
                <input type="text" id="tipo_equipo" name="tipo_equipo" required>
                
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="">Seleccione un estado</option>
                    <option value="nuevo">Nuevo</option>
                    <option value="usado">Buen estado</option>
                    <option value="dañado">Dañado</option>
                    <option value="en reparacion">En reparación</option>
                </select>
                
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
                
                <button type="submit" class="btn-update" onclick="return confirm('¿Estás seguro de que deseas agregar este equipo?');">Agregar Equipo</button>
            </form>
        </div>
        
        <div class="user-table">
            <h1>Equipos</h1>
            <ul>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>
                                <div>" . htmlspecialchars($row['Nombre']) . "</div>
                                <div>" . htmlspecialchars($row['Tipo_Equipo']) . "</div>
                                <div>" . htmlspecialchars($row['Estado']) . "</div>
                                <div>" . htmlspecialchars($row['Descripcion']) . "</div>
                                <button onclick=\"openEditModal(" . $row['id_Equipo'] . ", '" . htmlspecialchars($row['Nombre']) . "', '" . htmlspecialchars($row['Tipo_Equipo']) . "', '" . htmlspecialchars($row['Estado']) . "', '" . htmlspecialchars($row['Descripcion']) . "')\">Modificar</button>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='action' value='delete'>
                                    <input type='hidden' name='id_equipo' value='" . $row['id_Equipo'] . "'>
                                    <button type='submit' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este equipo?\");' class='btn-delete'>Eliminar</button>
                                </form>
                              </li>";
                    }
                } else {
                    echo "<li>No hay equipos.</li>";
                }
                ?>
            </ul>
        </div>
    </div> 
</div>

<!-- Modal for editing -->
<div id="editModal" style="display:none; position:fixed; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
    <div style="background:white; margin:100px auto; padding:20px; width:300px; border-radius:5px;">
        <h2>Modificar Equipo</h2>
        <form action="" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_id_equipo" name="id_equipo">
            <label for="edit_nombre">Nombre:</label>
            <input type="text" id="edit_nombre" name="nombre" required>
            
            <label for="edit_tipo_equipo">Tipo de Equipo:</label>
            <input type="text" id="edit_tipo_equipo" name="tipo_equipo" required>
            
            <label for="edit_estado">Estado:</label>
            <select id="edit_estado" name="estado" required>
                <option value="">Seleccione un estado</option>
                <option value="nuevo">Nuevo</option>
                <option value="usado">Buen estado</option>
                <option value="dañado">Dañado</option>
                <option value="en reparacion">En reparación</option>
            </select>
            
            <label for="edit_descripcion">Descripción:</label>
            <textarea id="edit_descripcion" name="descripcion" rows="4" required></textarea>
            
            <button type="submit" class="btn-update">Guardar Cambios</button>
            <button type="button" onclick="closeEditModal()">Cancelar</button>
        </form>
    </div>
</div>

</body>
</html>