<?php
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];

    if (!empty($nombre) && !empty($correo)) {

        $sqlCheck = "SELECT * FROM usuario WHERE Nombre = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("s", $nombre);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            echo "El usuario ya existe. Por favor, elige un nombre diferente.";
        } else {
    
            $sql = "INSERT INTO usuario (Nombre, Correo) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $nombre, $correo);

            if ($stmt->execute()) {
                header("Location: usuarios.php");
            } else {
                echo "Error al agregar el usuario: " . $stmt->error;
            }

            $stmt->close();
        }

        $stmtCheck->close();
    } else {
        echo "Por favor, complete todos los campos.";
    }

    $conn->close();
}
?>