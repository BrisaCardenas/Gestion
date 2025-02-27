<?php
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_Equipo = $_POST['id_Equipo'];

   
    $stmtDeleteAsignaciones = $conn->prepare("DELETE FROM asignaciones WHERE equipo_id_Equipo = ?");
    $stmtDeleteAsignaciones->bind_param("i", $id_Equipo);
    $stmtDeleteAsignaciones->execute();
    $stmtDeleteAsignaciones->close();

   
    $sql = "DELETE FROM equipo WHERE id_Equipo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_Equipo);

    if ($stmt->execute()) {
        echo "Equipo eliminado correctamente.";
    } else {
        echo "Error al eliminar el equipo: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: equipos.php");
    exit();
}
?>