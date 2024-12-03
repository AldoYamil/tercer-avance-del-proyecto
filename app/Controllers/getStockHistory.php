<?php
session_start();
include '../../config/connection.php';

header('Content-Type: application/json');

// Basic input validation
if (!isset($_GET['code'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No stock code provided']);
    exit;
}

if (!isset($_SESSION['num'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$code = $_GET['code'];
$operator_id = $_SESSION['num'];

try {
    // Get operator's warehouse
    $query = "SELECT warehouse FROM Employees WHERE num = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $operator_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $warehouse_code = $result->fetch_assoc()['warehouse'] ?? null;

    if (!$warehouse_code) {
        throw new Exception('Invalid warehouse access');
    }

    // Get stock movement history with item name
    $query = "SELECT 
                sm.date,
                sm.type,
                sm.amount,
                sm.reason,
                e.name as operator_name,
                s.name as item_name
              FROM StockMovement sm
              JOIN Employees e ON sm.operator = e.num
              JOIN Stock s ON sm.stock = s.code
              WHERE sm.stock = ? 
              AND sm.warehouse = ?
              ORDER BY sm.date DESC
              LIMIT 50";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $code, $warehouse_code);
    $stmt->execute();
    $result = $stmt->get_result();

    $movements = [];
    while ($row = $result->fetch_assoc()) {
        $movements[] = [
            'date' => date('Y-m-d H:i', strtotime($row['date'])),
            'type' => $row['type'] === 'IN' ? 'Added' : 'Removed',
            'amount' => $row['amount'],
            'reason' => htmlspecialchars($row['reason']),
            'operator' => htmlspecialchars($row['operator_name']),
            'item_name' => htmlspecialchars($row['item_name'])
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $movements
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}