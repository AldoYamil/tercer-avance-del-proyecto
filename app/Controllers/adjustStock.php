<?php
session_start();
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/operator/operatorDashboard.php?page=dashboard');
    exit;
}

// Get POST data
$code = $_POST['code'] ?? '';
$warehouse_code = $_POST['warehouse_code'] ?? '';
$adjustment_type = $_POST['adjustment_type'] ?? '';
$adjustment_amount = (int)($_POST['adjustment_amount'] ?? 0);
$adjustment_reason = $_POST['adjustment_reason'] ?? '';
$operator_id = $_SESSION['num'] ?? null;

if (!$code || !$warehouse_code || !$adjustment_amount || !$operator_id) {
    header('Location: ../../views/operator/operatorDashboard.php?page=dashboard&message=' . urlencode('Invalid input data'));
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Get current stock amount
    $query = "SELECT amount FROM Stock WHERE code = ? AND warehouse = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $code, $warehouse_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_stock = $result->fetch_assoc()['amount'] ?? 0;

    // Calculate new amount
    $new_amount = $adjustment_type === 'add' 
        ? $current_stock + $adjustment_amount 
        : $current_stock - $adjustment_amount;

    // Prevent negative stock
    if ($new_amount < 0) {
        throw new Exception('Cannot remove more stock than available');
    }

    // Update stock amount
    $query = "UPDATE Stock SET amount = ? WHERE code = ? AND warehouse = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $new_amount, $code, $warehouse_code);
    $stmt->execute();

    // Record stock movement
    $query = "INSERT INTO StockMovement (stock, warehouse, type, amount, reason, operator, date) 
              VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $movement_type = $adjustment_type === 'add' ? 'IN' : 'OUT';
    $stmt->bind_param("sssssi", 
        $code, 
        $warehouse_code, 
        $movement_type, 
        $adjustment_amount, 
        $adjustment_reason, 
        $operator_id
    );
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    $message = "Stock successfully adjusted";
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $message = "Error: " . $e->getMessage();
}

header('Location: ../../views/operator/operatorDashboard.php?page=dashboard&message=' . urlencode($message));
exit;