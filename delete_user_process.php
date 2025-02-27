<?php
session_start();
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'] ?? null; 

    if ($correo) {
        // Eliminar asignaciones relacionadas
        $stmtDeleteAsignaciones = $conn->prepare("DELETE FROM asignaciones WHERE usuario_Correo = ?");
        $stmtDeleteAsignaciones->bind_param("s", $correo);
        $stmtDeleteAsignaciones->execute();
        $stmtDeleteAsignaciones->close();

        // Eliminar incidentes relacionados
        $stmtDeleteIncidentes = $conn->prepare("DELETE FROM incidentes WHERE usuario_Correo = ?");
        $stmtDeleteIncidentes->bind_param("s", $correo);
        $stmtDeleteIncidentes->execute();
        $stmtDeleteIncidentes->close();

        // Eliminar el usuario
        $stmtDeleteUser  = $conn->prepare("DELETE FROM usuario WHERE Correo = ?");
        $stmtDeleteUser ->bind_param("s", $correo);
        if ($stmtDeleteUser ->execute()) {
            echo 'Usuario eliminado correctamente.';
        } else {
            echo 'Error al eliminar el usuario: ' . $stmtDeleteUser ->error;
        }
        $stmtDeleteUser ->close();
    } else {
        echo "Error: El correo no puede ser nulo.";
    }

    $conn->close();
    exit();
}
?>