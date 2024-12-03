<?php
include '../../config/connection.php';
session_start();

// Función helper para la redirección
function redirectToManageEmployee($message) {
    $base = $_SESSION['role'] === 'R001' 
        ? "../../views/admin/adminDashboard.php"
        : "../../views/supervisor/supervisorDashboard.php";
    header("Location: {$base}?page=manageEmployee&message=" . urlencode($message));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['num'])) {
    try {
        $num = intval($_GET['num']);
        
        $conn->begin_transaction();

        // Get the usernum before deleting
        $query = "SELECT usernum FROM Employees WHERE num = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $num);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();
        
        if (!$employee) {
            throw new Exception("Employee not found");
        }

        // Delete from Employees
        $deleteEmployeeQuery = "DELETE FROM Employees WHERE num = ?";
        $stmt = $conn->prepare($deleteEmployeeQuery);
        $stmt->bind_param("i", $num);
        
        if (!$stmt->execute()) {
            throw new Exception("Error deleting employee record");
        }

        // Delete from Users
        $deleteUserQuery = "DELETE FROM Users WHERE num = ?";
        $stmt = $conn->prepare($deleteUserQuery);
        $stmt->bind_param("i", $employee['usernum']);
        
        if (!$stmt->execute()) {
            throw new Exception("Error deleting user account");
        }

        $conn->commit();
        redirectToManageEmployee("Employee deleted successfully");
        
    } catch (Exception $e) {
        $conn->rollback();
        redirectToManageEmployee("Error: " . $e->getMessage());
    }
} else {
    redirectToManageEmployee("");
}

$conn->close();
?>