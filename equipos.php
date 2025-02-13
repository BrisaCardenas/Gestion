<?php
include 'db.php'; 
include 'sidebar.php'; 

$sql = "SELECT Nombre, Tipo_Equipo, Estado, Descripcion FROM equipo ORDER BY Nombre ASC"; 
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
        function updateCharacterCount() {
            const descriptionField = document.getElementById('descripcion');
            const characterCount = document.getElementById('characterCount');
            const maxLength = 100; 

            const currentLength = descriptionField.value.length;
            characterCount.textContent = `${currentLength} / ${maxLength}`;

          
            if (currentLength > maxLength) {
                characterCount.style.color = 'red';
            } else {
                characterCount.style.color = 'black';
            }
        }
    </script>
</head>
<body>
<div class="main-container">
    <div class="edit-form">
        <form action="updateEquipo.php" method="POST"> 
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
            <textarea id="descripcion" name="descripcion" rows="4" required style="width: 100%;" oninput="updateCharacterCount()"></textarea>
            <div id="characterCount" style="margin-top: 5px;">0 / 100</div>
             
            <button type="submit" class="btn-update">Agregar Equipo</button>
        </form>
    </div>
    
    <div class="user-table">
        <h1>Equipos</h1>
        <ul>
            <li class="columna">
                <div><strong>Nombre</strong></div>
                <div><strong>Tipo de Equipo</strong></div>
                <div><strong>Estado</strong></div>
                <div><strong>Descripción</strong></div>
            </li>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li>
                            <div>" . htmlspecialchars($row['Nombre']) . "</div>
                            <div>" . htmlspecialchars($row['Tipo_Equipo']) . "</div>
                            <div>" . htmlspecialchars($row['Estado']) . "</div>
                            <div>" . htmlspecialchars($row['Descripcion']) . "</div>
                          </li>";
                }
            } else {
                echo "<li>No hay equipos.</li>";
            }
            ?>
        </ul>
    </div>
</div>
</body>
</html>