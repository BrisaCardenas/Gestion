<?php
session_start();
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'] ?? null; 

    if ($correo) {
        // Verificar si el usuario tiene registros en asignaciones
        $stmtCheckAsignaciones = $conn->prepare("SELECT * FROM asignaciones WHERE usuario_Correo = ?");
        $stmtCheckAsignaciones->bind_param("s", $correo);
        $stmtCheckAsignaciones->execute();
        $resultAsignaciones = $stmtCheckAsignaciones->get_result();

        // Verificar si el usuario tiene registros en incidentes
        $stmtCheckIncidentes = $conn->prepare("SELECT * FROM incidentes WHERE usuario_Correo = ?");
        $stmtCheckIncidentes->bind_param("s", $correo);
        $stmtCheckIncidentes->execute();
        $resultIncidentes = $stmtCheckIncidentes->get_result();

        // Mensaje de advertencia si hay registros
        if ($resultAsignaciones->num_rows > 0 || $resultIncidentes->num_rows > 0) {
            echo "<script>
                    document.getElementById('confirmDeleteModal').style.display = 'flex';
                    var userEmail = '" . htmlspecialchars($correo) . "';
                  </script>";
        } else {
            // Si no hay registros, proceder a eliminar directamente
            $stmtDeleteUser   = $conn->prepare("DELETE FROM usuario WHERE Correo = ?");
            $stmtDeleteUser  ->bind_param("s", $correo);
            if ($stmtDeleteUser  ->execute()) {
                header("Location: usuarios.php");
                exit();
            } else {
                echo 'Error al eliminar el usuario: ' . $stmtDeleteUser  ->error;
            }
            $stmtDeleteUser  ->close();
        }

        $stmtCheckAsignaciones->close();
        $stmtCheckIncidentes->close();
    } else {
        echo "Error: El correo no puede ser nulo.";
    }

    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Usuario</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function closeModal() {
            document.getElementById('confirmDeleteModal').style.display = 'none';
        }

        function confirmDelete() {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_user_process.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert(xhr.responseText);
                    window.location.href = 'usuarios.php'; // Redirigir después de la eliminación
                }
            };
            xhr.send('correo=' + encodeURIComponent(userEmail) + '&action=delete');
            closeModal();
        }
    </script>
</head>
<body>

<!-- Modal para Confirmación de Eliminación -->
<div id="confirmDeleteModal" style="display:none; position:fixed; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); z-index:1000; display:flex; justify-content:center; align-items:center;">
    <div style="background:white; padding:20px; border-radius:5px; width:300px; text-align:center;">
        <h2>Advertencia</h2>
        <p>Este usuario tiene registros en asignaciones o incidentes. Si continúa, esos registros serán eliminados. ¿Desea continuar?</p>
        <button id="confirmDeleteButton" onclick="confirmDelete()">Sí, continuar</button>
        <button onclick="closeModal()">Cancelar</button>
    </div>
</div>

</body>
</html>