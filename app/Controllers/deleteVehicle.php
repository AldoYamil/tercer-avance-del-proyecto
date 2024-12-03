<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['license_plate'])) {
    try {
        $license_plate = trim(mysqli_real_escape_string($conn, $_GET['license_plate']));

        // Verificar si el vehículo existe
        $checkQuery = "SELECT license_plate FROM Vehicle WHERE license_plate = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $license_plate);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception("Vehicle not found");
        }

        // Eliminar vehículo
        $query = "DELETE FROM Vehicle WHERE license_plate = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $license_plate);
        
        if ($stmt->execute()) {
            header("Location: ../../views/admin/adminDashboard.php?page=manageVehicle&message=Vehicle deleted successfully");
        } else {
            throw new Exception("Error deleting vehicle");
        }
        
    } catch (Exception $e) {
        header("Location: ../../views/admin/adminDashboard.php?page=manageVehicle&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/admin/adminDashboard.php?page=manageVehicle");
}

$conn->close();
?>
