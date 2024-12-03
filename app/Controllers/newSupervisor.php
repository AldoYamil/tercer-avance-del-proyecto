<?php
include '../../config/connection.php';

function generateUsername($name, $lastname, $surname) {
    // Generar username usando las primeras letras del nombre y apellidos
    $username = strtolower(
        substr($name, 0, 2) . 
        substr($lastname, 0, 2) . 
        ($surname ? substr($surname, 0, 2) : '')
    );
    return $username;
}

function generateRandomPassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $length);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $surname = $_POST['surname'] ?? null;
        $warehouse = $_POST['warehouse_code'];
        $role = 'R001'; // Supervisor role
        
        $username = generateUsername($name, $lastname, $surname);
        $password = generateRandomPassword();
        $status = "available";

        $conn->begin_transaction();

        // Insertar en Users primero
        $insertUserQuery = "INSERT INTO Users (username, password, role) VALUES (?, ?, ?)";
        $stmtUser = $conn->prepare($insertUserQuery);
        $stmtUser->bind_param("sss", $username, $password, $role);
        
        if (!$stmtUser->execute()) {
            throw new Exception("Error creating user account");
        }
        
        $userId = $conn->insert_id;

        // Insertar en Employees
        $insertEmployeeQuery = "INSERT INTO Employees (name, lastname, surname, role, username, password, status, warehouse, usernum) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtEmployee = $conn->prepare($insertEmployeeQuery);
        $stmtEmployee->bind_param("ssssssssi", $name, $lastname, $surname, $role, $username, $password, $status, $warehouse, $userId);
        
        if (!$stmtEmployee->execute()) {
            throw new Exception("Error creating employee record");
        }

        $conn->commit();
        
        $message = "Supervisor added successfully. Username: $username, Password: $password";
        header("Location: ../../views/admin/adminDashboard.php?page=manageSupervisor&message=" . urlencode($message));
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: ../../views/admin/adminDashboard.php?page=manageSupervisor&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/admin/adminDashboard.php?page=manageSupervisor");
}

$conn->close();
?>