<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar y limpiar inputs
        $license_plate = trim(mysqli_real_escape_string($conn, $_POST['license_plate']));
        $model = trim(mysqli_real_escape_string($conn, $_POST['model']));
        $max_capacity = floatval($_POST['max_capacity']);
        $warehouse = trim(mysqli_real_escape_string($conn, $_POST['warehouse']));
        $status = trim(mysqli_real_escape_string($conn, $_POST['status']));

        // Verificar si el vehículo existe
        $checkQuery = "SELECT license_plate FROM Vehicle WHERE license_plate = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $license_plate);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception("Vehicle not found");
        }

        // Actualizar vehículo
        $query = "UPDATE Vehicle 
                 SET model = ?, 
                     max_capacity = ?, 
                     warehouse = ?, 
                     status = ? 
                 WHERE license_plate = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdsss", $model, $max_capacity, $warehouse, $status, $license_plate);
        
        if ($stmt->execute()) {
            header("Location: ../../views/admin/adminDashboard.php?page=manageVehicle&message=Vehicle updated successfully");
        } else {
            throw new Exception("Error updating vehicle");
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        header("Location: ../../views/admin/adminDashboard.php?page=manageVehicle&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/admin/adminDashboard.php?page=manageVehicle");
}

$conn->close();
?>
