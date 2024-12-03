<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['num'])) {
    try {
        $num = intval($_GET['num']);
        
        $conn->begin_transaction();

        // Obtener el usernum antes de eliminar
        $query = "SELECT usernum FROM Employees WHERE num = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $num);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();
        
        if (!$employee) {
            throw new Exception("Supervisor not found");
        }

        // Eliminar de Employees
        $deleteEmployeeQuery = "DELETE FROM Employees WHERE num = ?";
        $stmt = $conn->prepare($deleteEmployeeQuery);
        $stmt->bind_param("i", $num);
        
        if (!$stmt->execute()) {
            throw new Exception("Error deleting supervisor record");
        }

        // Eliminar de Users
        $deleteUserQuery = "DELETE FROM Users WHERE num = ?";
        $stmt = $conn->prepare($deleteUserQuery);
        $stmt->bind_param("i", $employee['usernum']);
        
        if (!$stmt->execute()) {
            throw new Exception("Error deleting user account");
        }

        $conn->commit();
        header("Location: ../../views/admin/adminDashboard.php?page=manageSupervisor&message=Supervisor deleted successfully");
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: ../../views/admin/adminDashboard.php?page=manageSupervisor&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/admin/adminDashboard.php?page=manageSupervisor");
}

$conn->close();
?>