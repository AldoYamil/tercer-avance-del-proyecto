<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar y obtener datos del formulario
        $warehouse_code = trim(mysqli_real_escape_string($conn, $_POST['warehouse_code']));
        $employee_num = trim(mysqli_real_escape_string($conn, $_POST['employee_num']));
        $vehicle_number = trim(mysqli_real_escape_string($conn, $_POST['vehicle_number']));
        $starting_date = trim(mysqli_real_escape_string($conn, $_POST['starting_date']));
        $arrival_date = trim(mysqli_real_escape_string($conn, $_POST['arrival_date']));
        
        if (!isset($_POST['packages']) || empty($_POST['packages'])) {
            throw new Exception("Please select at least one package");
        }
        $packages = array_map(function($pkg) use ($conn) {
            return trim(mysqli_real_escape_string($conn, $pkg));
        }, $_POST['packages']);

        // Obtener la dirección del último paquete para el end_point
        $lastPackage = end($packages);
        $sqlDestination = "SELECT CONCAT(C.street, ' ', C.number, ', ', C.colony) AS destination
                          FROM Shipment S
                          JOIN Client C ON S.client = C.num
                          WHERE S.num = ?";
        $stmt = $conn->prepare($sqlDestination);
        $stmt->bind_param("i", $lastPackage);
        $stmt->execute();
        $destResult = $stmt->get_result();
        $destination = $destResult->fetch_assoc()['destination'];

        // Generar ID de ruta único (id_ruta)
        $route_id = mt_rand(100000, 999999);  // Generamos el ID de ruta de forma aleatoria

        // Generar ID de orden de entrega único
        $orden_entrega = mt_rand(100000, 999999);

        // Iniciar transacción
        $conn->begin_transaction();

        try {
            // Insertar la ruta principal
            $sqlPath = "INSERT INTO Path (num, starting_point, end_point, est_arrival, starting_date, id_ruta) 
                       VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sqlPath);
            $stmt->bind_param("issssi", 
                $route_id,
                $warehouse_code,
                $destination,
                $arrival_date,
                $starting_date,
                $route_id // Aseguramos que el valor de id_ruta se inserte correctamente
            );
            $stmt->execute();

            // Actualizar estado del vehículo
            $sqlUpdateVehicle = "UPDATE Vehicle SET status = 'unavailable' WHERE number = ?";
            $stmt = $conn->prepare($sqlUpdateVehicle);
            $stmt->bind_param("i", $vehicle_number);
            $stmt->execute();

            // Actualizar estado del empleado
            $sqlUpdateEmployee = "UPDATE Employees SET status = 'unavailable' WHERE num = ?";
            $stmt = $conn->prepare($sqlUpdateEmployee);
            $stmt->bind_param("i", $employee_num);
            $stmt->execute();

              // Asignar vehículo al empleado en la tabla Vehicle_Assignment
            $sqlVehicleAssignment = "INSERT INTO Vehicle_Assignment (vehicle_number, employee_num, assigned_date)
            VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sqlVehicleAssignment);
            $stmt->bind_param("iis", $vehicle_number, $employee_num, $starting_date);
            $stmt->execute();

            // Insertar detalles de la ruta para cada paquete
            $sqlDetails = "INSERT INTO RutaDetalles (id_vehiculo, fecha, orden_entrega, id_paquete, direccion_destino, id_ruta) 
            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sqlDetails);

            foreach ($packages as $index => $package_id) {
                // Obtener dirección de destino del paquete
                $sqlPackageAddr = "SELECT CONCAT(C.street, ' ', C.number, ', ', C.colony) AS addr
                                    FROM Shipment S
                                    JOIN Client C ON S.client = C.num
                                    WHERE S.num = ?";
                $stmtAddr = $conn->prepare($sqlPackageAddr);
                $stmtAddr->bind_param("i", $package_id);
                $stmtAddr->execute();
                $addrResult = $stmtAddr->get_result();
                $package_addr = $addrResult->fetch_assoc()['addr'];

                // Insertar detalle de ruta (no se pasa el valor de 'ruta' porque es AUTO_INCREMENT)
                $stmt->bind_param("issisi", 
                    $vehicle_number,   // INT - id_vehiculo
                    $starting_date,    // STRING - fecha
                    $orden_entrega,    // INT - orden_entrega
                    $package_id,       // INT - id_paquete
                    $package_addr,     // STRING - direccion_destino
                    $route_id          // INT - id_ruta
                );
                $stmt->execute();

                // Actualizar el paquete
                $sqlUpdatePackage = "UPDATE Shipment SET vehicle = ?, path = ? WHERE num = ?";
                $stmtUpdate = $conn->prepare($sqlUpdatePackage);
                $stmtUpdate->bind_param("iii", $vehicle_number, $route_id, $package_id);
                $stmtUpdate->execute();
            }

            $conn->commit();
            header("Location: ../../views/supervisor/supervisorDashboard.php?page=managePath&message=Path created successfully");

        } catch (Exception $e) {
            $conn->rollback();
            throw new Exception("Error creating path: " . $e->getMessage());
        }

    } catch (Exception $e) {
        header("Location: ../../views/supervisor/supervisorDashboard.php?page=managePath&message=Error: " . urlencode($e->getMessage()));
    }
} else {
    header("Location: ../../views/supervisor/supervisorDashboard.php?page=managePath");
}

$conn->close();
?>