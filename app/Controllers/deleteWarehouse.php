<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
    try {
        $code = trim(mysqli_real_escape_string($conn, $_GET['code']));

        $query = "DELETE FROM Warehouse WHERE code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $code);
        
        if ($stmt->execute()) {
            header("Location: ../../views/admin/adminDashboard.php?page=manageWarehouse&message=Warehouse deleted successfully");
        } else {
            throw new Exception("Error deleting warehouse");
        }
    } catch (Exception $e) {
        header("Location: ../../views/admin/adminDashboard.php?page=manageWarehouse&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/admin/adminDashboard.php?page=manageWarehouse");
}

$conn->close();
?>