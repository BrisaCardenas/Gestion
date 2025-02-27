<?php
session_start();
include 'db.php'; 

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'] ?? null;
    $nombre = $_POST['nombre'] ?? null;

    if (!empty($correo) && !empty($nombre)) {
        $stmtUpdate = $conn->prepare("UPDATE usuario SET Nombre = ? WHERE Correo = ?");
        $stmtUpdate->bind_param("ss", $nombre, $correo);

        if ($stmtUpdate->execute()) {
            
            $historialStmt = $conn->prepare("INSERT INTO historial (tabla, accion, datos, Tipo_Accion, usuario_Correo) VALUES (?, ?, ?, ?, ?)");
            $historialStmt->bind_param("sssss", $table, $action, $data, $typeAction, $userEmail);

            $table = 'usuario'; 
            $action = 'update';
            $data = json_encode(['Nombre' => $nombre, 'Correo' => $correo]);
            $typeAction = 'Modificar';
            $userEmail = $_SESSION['email']; 

            $historialStmt->execute();
            $historialStmt->close();

            $message = "Usuario actualizado correctamente.";
        } else {
            $message = "Error al actualizar el usuario: " . $stmtUpdate->error;
        }

        $stmtUpdate->close();
    } else {
        $message = "Por favor, complete todos los campos.";
    }
}

if (isset($_GET['correo'])) {
    $correo = $_GET['correo'];
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE Correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>