<?php
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_Usuario = $_POST['id_Usuario'];


    $sql = "DELETE FROM usuario WHERE id_Usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_Usuario);

    if ($stmt->execute()) {
        echo "Usuario eliminado correctamente.";
    } else {
        echo "Error al eliminar el usuario: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: usuarios.php");
    exit();
}
?>
?>