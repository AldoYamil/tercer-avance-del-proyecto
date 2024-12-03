<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar y limpiar inputs
        $code = trim(mysqli_real_escape_string($conn, $_POST['code']));
        $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
        $street = trim(mysqli_real_escape_string($conn, $_POST['street']));
        $colony = trim(mysqli_real_escape_string($conn, $_POST['colony']));
        $number = trim(mysqli_real_escape_string($conn, $_POST['number']));

        // Actualizar almacén
        $query = "UPDATE Warehouse 
                 SET name = ?, street = ?, colony = ?, number = ? 
                 WHERE code = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $name, $street, $colony, $number, $code);
        
        if ($stmt->execute()) {
            header("Location: ../../views/admin/adminDashboard.php?page=manageWarehouse&message=Warehouse updated successfully");
        } else {
            throw new Exception("Error updating warehouse");
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        header("Location: ../../views/admin/adminDashboard.php?page=manageWarehouse&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/admin/adminDashboard.php?page=manageWarehouse");
}

$conn->close();
?>
