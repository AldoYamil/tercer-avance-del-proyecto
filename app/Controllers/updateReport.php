<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['num']) && isset($_GET['status'])) {
    try {
        $num = trim(mysqli_real_escape_string($conn, $_GET['num']));
        $status = trim(mysqli_real_escape_string($conn, $_GET['status']));

        // Verificar que el status sea válido
        if (!in_array($status, ['open', 'close'])) {
            throw new Exception("Invalid status");
        }

        // Verificar si el reporte existe
        $checkQuery = "SELECT num FROM Incident WHERE num = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("i", $num);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception("Report not found");
        }

        // Actualizar el status del reporte
        $query = "UPDATE Incident SET status = ? WHERE num = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $num);
        
        if ($stmt->execute()) {
            $message = $status === 'close' ? 'Report closed successfully' : 'Report reopened successfully';
            header("Location: ../../views/admin/adminDashboard.php?page=manageReport&message=" . urlencode($message));
        } else {
            throw new Exception("Error updating report status");
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