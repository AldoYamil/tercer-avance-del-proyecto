<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar y limpiar inputs
        $license_plate = trim(mysqli_real_escape_string($conn, $_POST['license_plate']));
        $model = trim(mysqli_real_escape_string($conn, $_POST['model']));
        $max_capacity = floatval($_POST['max_capacity']);
        $warehouse = trim(mysqli_real_escape_string($conn, $_POST['warehouse']));
        $status = 'available'; // Estado por defecto

        // Verificar si la placa ya existe
        $checkQuery = "SELECT license_plate FROM Vehicle WHERE license_plate = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $license_plate);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("License plate already exists");
        }

        // Verificar si el almacén existe
        $checkWarehouseQuery = "SELECT code FROM Warehouse WHERE code = ?";
        $stmt = $conn->prepare($checkWarehouseQuery);
        $stmt->bind_param("s", $warehouse);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception("Selected warehouse does not exist");
        }

        // Insertar nuevo vehículo
        $query = "INSERT INTO Vehicle (license_plate, model, max_capacity, status, warehouse) 
                 VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdss", $license_plate, $model, $max_capacity, $status, $warehouse);
        
        if ($stmt->execute()) {
            header("Location: ../../views/admin/adminDashboard.php?page=manageVehicle&message=Vehicle added successfully");
        } else {
            throw new Exception("Error creating vehicle");
        }
        $stmt->close();
    } catch (Exception $e) {
    header("Location: ../../views/admin/adminDashboard.php?page=manageVehicle&message=Error: " . urlencode($e->getMessage()));
    }
    } else {
    header("Location: ../../views/admin/adminDashboard.php?page=manageVehicle");
    }
    $conn->close();
    ?>