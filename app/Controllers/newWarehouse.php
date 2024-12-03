<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar y limpiar inputs
        $code = trim(mysqli_real_escape_string($conn, $_POST['code']));
        $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
        $street = trim(mysqli_real_escape_string($conn, $_POST['street']));
        $colony = trim(mysqli_real_escape_string($conn, $_POST['colony']));
        $number = trim(mysqli_real_escape_string($conn, $_POST['number']));

        // Verificar si el código ya existe
        $checkQuery = "SELECT code FROM Warehouse WHERE code = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Warehouse code already exists");
        }

        // Insertar nuevo almacén
        $query = "INSERT INTO Warehouse (code, name, street, colony, number) 
                 VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $code, $name, $street, $colony, $number);
        
        if ($stmt->execute()) {
            header("Location: ../../views/admin/adminDashboard.php?page=manageWarehouse&message=Warehouse added successfully");
        } else {
            throw new Exception("Error creating warehouse");
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        header("Location: ../../views/admin/adminDashboard.php?page=manageWarehouse&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/admin/adminDashboard.php?page=manageWarehouse");
}

$conn->close();
?>
