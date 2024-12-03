<?php
include '../../config/connection.php';

session_start();

// Helper function for redirecting with message
function redirectWithMessage($type, $message) {
    header("Location: ../../views/supervisor/supervisorDashboard.php?page=newPackage&$type=" . urlencode($message));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $clientId = $_POST['client'] ?? null;
    $insuranceId = $_POST['insurance'] ?? null;
    $shipmentDate = $_POST['shipment_date'] ?? null;
    $deliveryDate = $_POST['delivery_date'] ?? null;
    $items = $_POST['item'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    if (!$clientId || !$insuranceId || !$shipmentDate || !$deliveryDate) {
        redirectWithMessage('error', 'Essential data is missing to process the order.');
    }

    $supervisorId = $_SESSION['num'] ?? null;
    if (!$supervisorId) {
        redirectWithMessage('error', 'Supervisor code not found.');
    }

    $query = "SELECT warehouse FROM Employees WHERE num = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $supervisorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $supervisorWarehouse = $row['warehouse'] ?? null;

    if (!$supervisorWarehouse) {
        redirectWithMessage('error', 'Could not obtain warehouse code.');
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        $createShipmentQuery = "INSERT INTO Shipment (date, delivery_date, client, insurance, warehouse) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($createShipmentQuery);
        $stmt->bind_param("sssds", $shipmentDate, $deliveryDate, $clientId, $insuranceId, $supervisorWarehouse);
        
        if (!$stmt->execute()) {
            throw new Exception("Error creating the order.");
        }
        
        $shipmentId = $conn->insert_id;

        if (!empty($items) && is_array($items)) {
            foreach ($items as $index => $itemCode) {
                $quantity = $quantities[$index] ?? 0;
                if ($quantity <= 0) continue;

                // Check available stock
                $checkStockQuery = "SELECT amount FROM Stock WHERE code = ? AND warehouse = ?";
                $checkStmt = $conn->prepare($checkStockQuery);
                $checkStmt->bind_param("ss", $itemCode, $supervisorWarehouse);
                $checkStmt->execute();
                $stockResult = $checkStmt->get_result();
                $currentStock = $stockResult->fetch_assoc()['amount'] ?? 0;

                if ($currentStock < $quantity) {
                    throw new Exception("Insufficient stock for product code $itemCode");
                }

                // Insert package
                $addPackageQuery = "INSERT INTO Package (shipment, stock, amount) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($addPackageQuery);
                $stmt->bind_param("isd", $shipmentId, $itemCode, $quantity);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error adding products to package.");
                }

                // Update stock
                $updateStockQuery = "UPDATE Stock SET amount = amount - ? WHERE code = ? AND warehouse = ?";
                $updateStmt = $conn->prepare($updateStockQuery);
                $updateStmt->bind_param("dss", $quantity, $itemCode, $supervisorWarehouse);
                
                if (!$updateStmt->execute()) {
                    throw new Exception("Error updating stock.");
                }
            }
        } else {
            throw new Exception("No products were selected for the order.");
        }

        // Record in Record table
        $recordLocationQuery = "SELECT name FROM Warehouse WHERE code = ?";
        $stmt = $conn->prepare($recordLocationQuery);
        $stmt->bind_param("s", $supervisorWarehouse);
        $stmt->execute();
        $result = $stmt->get_result();
        $warehouseName = $result->fetch_assoc()['name'] ?? 'Unknown';

        $insertRecordQuery = "INSERT INTO Record (date, location, status, client, shipment) VALUES (?, ?, 'Order Placed', ?, ?)";
        $stmt = $conn->prepare($insertRecordQuery);
        $stmt->bind_param("ssii", $shipmentDate, $warehouseName, $clientId, $shipmentId);
        
        if (!$stmt->execute()) {
            throw new Exception("Error recording the shipment.");
        }

        // Commit transaction
        $conn->commit();
        redirectWithMessage('success', 'Package and order have been created successfully.');

    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        redirectWithMessage('error', $e->getMessage());
    }
} else {
    redirectWithMessage('error', 'Invalid request method.');
}
?>