<?php
include '../../config/connection.php';
session_start();

function redirectToManageEmployee($message) {
    $base = $_SESSION['role'] === 'R005' 
        ? "../../views/admin/adminDashboard.php"
        : "../../views/supervisor/supervisorDashboard.php";
    header("Location: {$base}?page=manageEmployee&message=" . urlencode($message));
    exit();
}

function generateUsername($name, $lastname, $surname) {
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

function validateNameField($field, $fieldName) {
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $field)) {
        throw new Exception("The $fieldName field can only contain letters.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $surname = $_POST['surname'] ?? null;
        $warehouse = $_POST['warehouse_code'];
        $role = $_POST['role'];

        validateNameField($name, "nombre");
        validateNameField($lastname, "apellido paterno");
        if ($surname) {
            validateNameField($surname, "apellido materno");
        }
        
        $username = generateUsername($name, $lastname, $surname);
        $password = generateRandomPassword();
        $status = "available";

        $conn->begin_transaction();

        // Insert into Users first
        $insertUserQuery = "INSERT INTO Users (username, password, role) VALUES (?, ?, ?)";
        $stmtUser = $conn->prepare($insertUserQuery);
        $stmtUser->bind_param("sss", $username, $password, $role);
        
        if (!$stmtUser->execute()) {
            throw new Exception("Error creating user account");
        }
        
        $userId = $conn->insert_id;

        // Insert into Employees
        $insertEmployeeQuery = "INSERT INTO Employees (name, lastname, surname, role, username, password, status, warehouse, usernum) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtEmployee = $conn->prepare($insertEmployeeQuery);
        $stmtEmployee->bind_param("ssssssssi", $name, $lastname, $surname, $role, $username, $password, $status, $warehouse, $userId);
        
        if (!$stmtEmployee->execute()) {
            throw new Exception("Error creating employee record");
        }

        $conn->commit();
        
        $message = "Employee added successfully. Username: $username, Password: $password";
        redirectToManageEmployee($message);
        
    } catch (Exception $e) {
        $conn->rollback();
        redirectToManageEmployee("Error: " . $e->getMessage());
    }
} else {
    redirectToManageEmployee("");
}

$conn->close();
?>
