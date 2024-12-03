<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $num = $_POST['num'];
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $surname = $_POST['surname'] ?? null;
        $warehouse = $_POST['warehouse_code'];
        $status = $_POST['status'];

        // Actualizar empleado
        $query = "UPDATE Employees 
                 SET name = ?, lastname = ?, surname = ?, warehouse = ?, status = ? 
                 WHERE num = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $name, $lastname, $surname, $warehouse, $status, $num);
        
        if ($stmt->execute()) {
            header("Location: ../../views/admin/adminDashboard.php?page=manageSupervisor&message=Supervisor updated successfully");
        } else {
            throw new Exception("Error updating supervisor");
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        header("Location: ../../views/admin/adminDashboard.php?page=manageSupervisor&message=Error: " . $e->getMessage());
    }
} else {
    header("Location: ../../views/admin/adminDashboard.php?page=manageSupervisor");
}

$conn->close();
?>
