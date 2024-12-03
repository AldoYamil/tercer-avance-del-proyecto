<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar y limpiar inputs
        $code = trim(mysqli_real_escape_string($conn, $_POST['code']));
        $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
        $amount = intval($_POST['amount']);
        $warehouse_code = trim(mysqli_real_escape_string($conn, $_POST['warehouse_code']));

        // Verificar si el código ya existe
        $checkQuery = "SELECT code FROM Stock WHERE code = ? AND warehouse = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ss", $code, $warehouse_code);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Item code already exists in this warehouse");
        }

        // Iniciar transacción
        $conn->begin_transaction();

        try {
            // Insertar en Stock
            $query = "INSERT INTO Stock (code, name, amount, warehouse) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssis", $code, $name, $amount, $warehouse_code);
            $stmt->execute();

            // Insertar en Item si no existe
            $query = "INSERT IGNORE INTO Item (code, name) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $code, $name);
            $stmt->execute();

            $conn->commit();
            header("Location: ../../views/supervisor/supervisorDashboard.php?page=manageStock&message=Item added successfully");
        } catch (Exception $e) {
            $conn->rollback();
            throw new Exception("Error adding item: " . $e->getMessage());
        }
    } catch (Exception $e) {
        header("Location: ../../views/supervisor/supervisorDashboard.php?page=manageStock&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/supervisor/supervisorDashboard.php?page=manageStock");
}

$conn->close();
?> 