<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/supervisor/supervisorDashboard.php?page=manageStock");
    exit();
}

try {
    // Validate and sanitize inputs
    $code = trim(mysqli_real_escape_string($conn, $_POST['code']));
    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $description = trim(mysqli_real_escape_string($conn, $_POST['description']));
    $amount = intval($_POST['amount']);
    $warehouse_code = trim(mysqli_real_escape_string($conn, $_POST['warehouse_code']));

    // Start transaction
    $conn->begin_transaction();
    
    // Update Stock
    $stmt = $conn->prepare("UPDATE Stock SET name = ?, amount = ? WHERE code = ? AND warehouse = ?");
    $stmt->bind_param("siss", $name, $amount, $code, $warehouse_code);
    $stmt->execute();

    // Update Item
    $stmt = $conn->prepare("UPDATE Item SET name = ?, description = ? WHERE code = ?");
    $stmt->bind_param("sss", $name, $description, $code);
    $stmt->execute();

    $conn->commit();
    $redirectMessage = "Item updated successfully";
    
} catch (Exception $e) {
    $conn->rollback();
    $redirectMessage = "Error: " . $e->getMessage();
} finally {
    $conn->close();
    header("Location: ../../views/supervisor/supervisorDashboard.php?page=manageStock&message=" . urlencode($redirectMessage));
    exit();
}
?>