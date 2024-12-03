<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['num'])) {
    try {
        $conn->begin_transaction();

        $num = trim(mysqli_real_escape_string($conn, $_GET['num']));

        // Verificar si el reporte existe
        $checkQuery = "SELECT num FROM Incident WHERE num = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("i", $num);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception("Report not found");
        }

        // Actualizar los envíos relacionados (poner incident a NULL)
        $updateShipmentQuery = "UPDATE Shipment SET incident = NULL WHERE incident = ?";
        $stmt = $conn->prepare($updateShipmentQuery);
        $stmt->bind_param("i", $num);
        if (!$stmt->execute()) {
            throw new Exception("Error updating related shipments");
        }

        // Eliminar el reporte
        $deleteQuery = "DELETE FROM Incident WHERE num = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $num);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting report");
        }

        $conn->commit();
        header("Location: ../../views/admin/adminDashboard.php?page=manageReport&message=Report deleted successfully");
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: ../../views/admin/adminDashboard.php?page=manageReport&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/admin/adminDashboard.php?page=manageReport");
}

$conn->close();
?>