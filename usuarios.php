<?php
session_start(); 
include 'db.php'; 
include 'sidebar.php'; 

$sql = "SELECT Nombre, Correo FROM usuario ORDER BY Nombre ASC"; 
$result = $conn->query($sql);

$message = "";
$nombre = ""; 
$correo = ""; 

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
            $correo = ""; 
        } else {
            
            $sqlInsert = "INSERT INTO usuario (Nombre, Correo) VALUES (?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ss", $nombre, $correo);

            if ($stmtInsert->execute()) {
                
                $historialStmt = $conn->prepare("INSERT INTO historial (tabla, accion, datos, Tipo_Accion, usuario_Correo) VALUES (?, ?, ?, ?, ?)");
                $historialStmt->bind_param("sssss", $table, $action, $data, $typeAction, $userEmail);
                
                $table = 'usuario';
                $action = 'insert';
                $data = json_encode(['Nombre' => $nombre, 'Correo' => $correo]);
                $typeAction = 'Agregar';
                $userEmail = $_SESSION['email']; 
                
                $historialStmt->execute();
                $historialStmt->close(); 

                $_SESSION['confirmation_message'] = "Usuario agregado correctamente."; 
                header("Location: usuarios.php");
                exit();
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

if (isset($_SESSION['confirmation_message'])) {
    $confirmation_message = $_SESSION['confirmation_message'];
    unset($_SESSION['confirmation_message']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personas JX</title>
    <link rel="stylesheet" href="usuarios.css">
    <link rel="stylesheet" href="style2.css">
    <script>
        function confirmAddUser () {
            return confirm("¿Estás seguro de que deseas agregar este usuario?");
        }
    </script>
</head>
<body>
<div class="main-container"> 
    <div class="edit-form">
        <?php if (!empty($message)): ?>
            <div class="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($confirmation_message)): ?>
            <div class="alert" style="background-color: #d4edda; color: #155724;">
                <?php echo htmlspecialchars($confirmation_message); ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="POST"> 
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>
             
            <button type="submit" class="btn-update" onclick="return confirmAddUser ();">Agregar Usuario</button>
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
                            <form action='delete_user.php' method='POST' style='display:inline;'>
                                <input type='hidden' name='correo' value='" . htmlspecialchars($row['Correo']) . "'>
                                <button type='submit' onclick='return confirm(\"¿Estás seguro de que deseas eliminar a " . htmlspecialchars($row['Nombre']) . "?\");' class='btn-delete'>Eliminar</button>
                            </form>
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
