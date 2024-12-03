<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_ruta'])) {
    try {
        $route_id = trim(mysqli_real_escape_string($conn, $_GET['id_ruta']));

        // Verificar si la ruta existe
        $checkQuery = "SELECT id_ruta FROM Path WHERE num = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("i", $route_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception("Route not foundID recibido: " . $route_id);
        }

        // Iniciar transacción
        $conn->begin_transaction();

        try {
            // Obtener los vehículos asociados a la ruta
            $vehicleQuery = "SELECT DISTINCT id_vehiculo FROM RutaDetalles WHERE id_ruta = ?";
            $stmt = $conn->prepare($vehicleQuery);
            $stmt->bind_param("i", $route_id);
            $stmt->execute();
            $vehicles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Actualizar el estado de los vehículos a 'available'
            if (!empty($vehicles)) {
                $updateVehicleQuery = "UPDATE Vehicle SET status = 'available' WHERE number IN (?)";
                $stmt = $conn->prepare($updateVehicleQuery);
                foreach ($vehicles as $vehicle) {
                    $stmt->bind_param("i", $vehicle['id_vehiculo']);
                    $stmt->execute();
                }
            }

            // Actualizar la tabla Shipment, estableciendo Path a NULL para los registros relacionados con la ruta
            $updateShipmentQuery = "UPDATE Shipment SET Path = NULL WHERE Path = ?";
            $stmt = $conn->prepare($updateShipmentQuery);
            $stmt->bind_param("i", $route_id);
            $stmt->execute();

            // Eliminar los detalles de la ruta
            $deleteDetailsQuery = "DELETE FROM RutaDetalles WHERE id_ruta = ?";
            $stmt = $conn->prepare($deleteDetailsQuery);
            $stmt->bind_param("i", $route_id);
            $stmt->execute();

            // Eliminar la ruta principal
            $deletePathQuery = "DELETE FROM Path WHERE num = ?";
            $stmt = $conn->prepare($deletePathQuery);
            $stmt->bind_param("i", $route_id);
            $stmt->execute();

            $conn->commit();
            header("Location: ../../views/supervisor/supervisorDashboard.php?page=managePath&message=Route deleted successfully");
        } catch (Exception $e) {
            $conn->rollback();
            throw new Exception("Error deleting route: " . $e->getMessage());
        }
        
    } catch (Exception $e) {
        header("Location: ../../views/supervisor/supervisorDashboard.php?page=managePath&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/supervisor/supervisorDashboard.php?page=managePath");
}

$conn->close();
?> 