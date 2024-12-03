<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code']) && isset($_GET['warehouse'])) {
    try {
        $code = trim(mysqli_real_escape_string($conn, $_GET['code']));
        $warehouse = trim(mysqli_real_escape_string($conn, $_GET['warehouse']));

        // Iniciar transacción
        $conn->begin_transaction();

        try {
            // Eliminar de Stock
            $query = "DELETE FROM Stock WHERE code = ? AND warehouse = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $code, $warehouse);
            $stmt->execute();

            // Verificar si el item existe en otros almacenes
            $query = "SELECT COUNT(*) as count FROM Stock WHERE code = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            // Si no existe en otros almacenes, eliminar de Item
            if ($row['count'] == 0) {
                $query = "DELETE FROM Item WHERE code = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $code);
                $stmt->execute();
            }

            $conn->commit();
            header("Location: ../../views/supervisor/supervisorDashboard.php?page=manageStock&message=Item deleted successfully");
        } catch (Exception $e) {
            $conn->rollback();
            throw new Exception("Error deleting item: " . $e->getMessage());
        }
    } catch (Exception $e) {
        header("Location: ../../views/supervisor/supervisorDashboard.php?page=manageStock&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/supervisor/supervisorDashboard.php?page=manageStock");
}

$conn->close();
?>