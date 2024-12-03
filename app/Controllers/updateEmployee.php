<?php
include '../../config/connection.php';
session_start();

function redirectToManageEmployee($message) {
    $base = $_SESSION['role'] === 'R005' 
        ? "../../views/admin/adminDashboard.php"
        : "../../views/supervisor/supervisorDashboard.php";
    header("Location: {$base}?page=manageEmployee&message=" . urlencode($message));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $num = $_POST['num'];
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $surname = $_POST['surname'] ?? null;
        $warehouse = $_POST['warehouse_code'];
        $role = $_POST['role'];
        $status = $_POST['status'];

        // Update employee
        $query = "UPDATE Employees 
                 SET name = ?, lastname = ?, surname = ?, warehouse = ?, role = ?, status = ? 
                 WHERE num = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $name, $lastname, $surname, $warehouse, $role, $status, $num);
        
        if ($stmt->execute()) {
            redirectToManageEmployee("Employee updated successfully");
        } else {
            throw new Exception("Error updating employee");
        }
        
        
    } catch (Exception $e) {
        redirectToManageEmployee("Error: " . $e->getMessage());
    }
} else {
    redirectToManageEmployee("");
}
?>
