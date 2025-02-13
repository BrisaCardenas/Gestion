<?php
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $tipo_equipo = $_POST['tipo_equipo'];
    $estado = $_POST['estado']; 
    $descripcion = $_POST['descripcion'];

    if (!empty($nombre) && !empty($tipo_equipo) && !empty($estado) && !empty($descripcion)) {
        
        $sql = "INSERT INTO equipo (Nombre, Tipo_Equipo, Estado, Descripcion) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $tipo_equipo, $estado, $descripcion);

        if ($stmt->execute()) {
            header("Location: equipos.php");
        } else {
            echo "Error al agregar el equipo: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Por favor, complete todos los campos.";
    }

    $conn->close();
}
?>