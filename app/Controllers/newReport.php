<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar y limpiar inputs
        $title = trim(mysqli_real_escape_string($conn, $_POST['title']));
        $description = trim(mysqli_real_escape_string($conn, $_POST['description']));
        $status = 'open'; // Siempre crear como open
        $date = date('Y-m-d H:i:s');
        
        session_start();
        $user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

        $query = "INSERT INTO Incident (title, description, date, user, status) 
                 VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssis", $title, $description, $date, $user, $status);
        
        if ($stmt->execute()) {
            header("Location: ../../views/admin/adminDashboard.php?page=manageReport&message=Report added successfully");
        } else {
            throw new Exception("Error creating report");
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        header("Location: ../../views/admin/adminDashboard.php?page=manageReport&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/admin/adminDashboard.php?page=manageReport");
}

$conn->close();
?>
