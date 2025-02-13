<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php'; 
include 'sidebar.php'; 

$sql = "SELECT Nombre, Correo FROM usuario ORDER BY Nombre ASC"; 
$result = $conn->query($sql);


$message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];

    if (!empty($nombre) && !empty($correo)) {
        
        $sqlCheck = "SELECT * FROM usuario WHERE Correo = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("s", $correo);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $message = "Error: El correo electrónico ya está en uso. Por favor, elige otro.";
        } else {
            
            $sqlInsert = "INSERT INTO usuario (Nombre, Correo) VALUES (?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ss", $nombre, $correo);

            if ($stmtInsert->execute()) {
                $message = "Usuario agregado correctamente.";
            } else {
                $message = "Error al agregar el usuario: " . $stmtInsert->error;
            }

            $stmtInsert->close();
        }

        $stmtCheck->close();
    } else {
        $message = "Por favor, complete todos los campos.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personas JX</title>
    <link rel="stylesheet" href="usuarios.css">
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
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>
             
            <button type="submit" class="btn-update">Agregar Usuario</button>
        </form>

    </div>
    
    <div class="user-table">
        <h1>Personas JX</h1>
        <ul>
            <?php
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li>
                            <strong>" . htmlspecialchars($row['Nombre']) . "</strong> - " . htmlspecialchars($row['Correo']) . "
                          </li>";
                }
            } else {
                echo "<li>No hay usuarios.</li>";
            }
            ?>
        </ul>
    </div>
</div> 
</body>
</html>
