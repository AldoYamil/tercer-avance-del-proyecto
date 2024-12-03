<?php 
include '../../config/connection.php';

if (!isset($_SESSION['num'])) {
    die("Unauthorized access. Please log in.");
}

$employee_num = $_SESSION['num'];

$sqlEmployee = "SELECT num, name, lastname, surname, role, warehouse FROM Employees WHERE num = ?";
$stmtEmployee = $conn->prepare($sqlEmployee);
$stmtEmployee->bind_param("i", $employee_num);
$stmtEmployee->execute();
$employee = $stmtEmployee->get_result()->fetch_assoc();
$stmtEmployee->close();

if (!$employee) {
    die("Employee not found.");
}

$sqlRoutes = "SELECT DISTINCT p.num AS route_id, v.number AS vehicle_id, v.license_plate, 
           p.starting_point, p.end_point, p.starting_date, p.est_arrival
    FROM RutaDetalles rd
    JOIN Vehicle v ON rd.id_vehiculo = v.number
    JOIN Path p ON rd.id_ruta = p.num
    WHERE v.number IN (
        SELECT vehicle_number 
        FROM Vehicle_Assignment 
        WHERE employee_num = ?)";
$stmtRoutes = $conn->prepare($sqlRoutes);
$stmtRoutes->bind_param("i", $employee_num);
$stmtRoutes->execute();
$routes = $stmtRoutes->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtRoutes->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_route'])) {
    $route_id = $_POST['route_id'];

    $_SESSION['started_route'] = $route_id;

    $sqlUpdateStatus = "INSERT INTO Record (date, status, shipment, client, location)
                        SELECT CURDATE(), 'In Transit', s.num, s.client, 
                        (SELECT CONCAT(w.street, ', ', w.colony, ' ', w.number) 
                         FROM Warehouse w WHERE w.code = ?) 
                        FROM Shipment s
                        JOIN RutaDetalles rd ON rd.id_paquete = s.num
                        WHERE rd.id_ruta = ?";
    $stmtUpdateStatus = $conn->prepare($sqlUpdateStatus);
    $stmtUpdateStatus->bind_param("si", $employee['warehouse'], $route_id);

    if ($stmtUpdateStatus->execute()) {
        echo "Route started, and order status updated to 'In Transit' with warehouse location.";
    } else {
        echo "Error updating order status: " . $stmtUpdateStatus->error;
    }
    $stmtUpdateStatus->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete_order'])) {
    $shipment_id = $_POST['shipment_id'];

    $sqlCompleteOrder = "INSERT INTO Record (date, status, shipment, client, location)
                         VALUES (CURDATE(), 'Delivered', ?, 
                                 (SELECT client FROM Shipment WHERE num = ?), 
                                 (SELECT CONCAT(street, ', ', colony, ' ', number) 
                                  FROM Client 
                                  WHERE num = (SELECT client FROM Shipment WHERE num = ?)))";
    $stmtCompleteOrder = $conn->prepare($sqlCompleteOrder);
    $stmtCompleteOrder->bind_param("iii", $shipment_id, $shipment_id, $shipment_id);

    if ($stmtCompleteOrder->execute()) {
        echo "Order marked as completed, and status updated to 'Delivered' with client location.";
    } else {
        echo "Error completing order: " . $stmtCompleteOrder->error;
    }
    $stmtCompleteOrder->close();
}
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finish_route'])) {
        unset($_SESSION['started_route']); // Limpiar la ruta iniciada de la sesión
        $vehicle_id = $_POST['vehicle_id'];

    $sqlUpdateVehicleStatus = "UPDATE Vehicle SET status = 'available' WHERE number = ?";
    $stmtUpdateVehicleStatus = $conn->prepare($sqlUpdateVehicleStatus);
    $stmtUpdateVehicleStatus->bind_param("i", $vehicle_id);

    if ($stmtUpdateVehicleStatus->execute()) {
        echo "Vehicle status updated to 'Available'.";
    } else {
        echo "Error updating vehicle status: " . $stmtUpdateVehicleStatus->error;
    }
    $stmtUpdateVehicleStatus->close();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finish_route']))  {  
    $employee_id = $_SESSION['num'];

    $sqlUpdateEmployee = "UPDATE Employees SET status = 'available' WHERE num = ?";
    $stmt = $conn->prepare($sqlUpdateEmployee);
    $stmt->bind_param("i", $employee_id); 
    if ($stmt->execute()) { 
        echo "Employee status updated to 'Available'."; 
    } else {
        echo "Error updating employee status: " . $stmt->error; 
    }
    $stmt->close();  
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Routes and Assigned Orders</title>
</head>
<body>
    <!-- Employee Info Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <i class="bi bi-person-circle fs-1 text-primary"></i>
                </div>
                <div class="col">
                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($employee['name'] . ' ' . $employee['lastname'] . ' ' . $employee['surname']); ?></h5>
                    <div class="text-muted small">
                        <span class="me-3">ID: <?php echo htmlspecialchars($employee['num']); ?></span>
                        <span class="me-3">Role: <?php echo htmlspecialchars($employee['role']); ?></span>
                        <span>Warehouse: <?php echo htmlspecialchars($employee['warehouse']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Routes Section -->
    <div class="card shadow-sm">
        <div class="card-header bg-body">
            <h5 class="card-title mb-0">Assigned Routes</h5>
        </div>
        <div class="card-body">
            <?php if (count($routes) > 0): ?>
                <div class="accordion" id="routesAccordion">
                    <?php foreach ($routes as $index => $route): ?>
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#route<?php echo $route['route_id']; ?>">
                                    <div class="d-flex align-items-center w-100">
                                        <div class="me-auto">
                                            <strong>Route #<?php echo htmlspecialchars($route['route_id']); ?></strong>
                                            <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($route['license_plate']); ?></span>
                                        </div>
                                        <small class="text-muted ms-2"><?php echo htmlspecialchars($route['starting_date']); ?></small>
                                    </div>
                                </button>
                            </h2>
                            <div id="route<?php echo $route['route_id']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>">
                                <div class="accordion-body">
                                    <div class="btn-group mb-4">
                                        <form method="POST" class="me-2">
                                            <input type="hidden" name="route_id" value="<?php echo htmlspecialchars($route['route_id']); ?>">
                                            <button type="submit" name="start_route" class="btn btn-primary">
                                                <i class="bi bi-play-fill"></i> Start Route
                                            </button>
                                        </form>
                                        <?php if (isset($_SESSION['started_route']) && $_SESSION['started_route'] == $route['route_id']): ?>
                                            <form method="POST">
                                                <input type="hidden" name="vehicle_id" value="<?php echo htmlspecialchars($route['vehicle_id']); ?>">
                                                <button type="submit" name="finish_route" class="btn btn-success">
                                                    <i class="bi bi-check-lg"></i> Finish Route
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Orders List -->
                                    <h6 class="mb-3">Orders in this Route</h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Client</th>
                                                    <th>Address</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sqlOrders = "SELECT s.num AS shipment_id, c.name AS client_name, c.street, c.colony, c.number
                                                              FROM Shipment s
                                                              JOIN RutaDetalles rd ON rd.id_paquete = s.num
                                                              JOIN Client c ON c.num = s.client
                                                              WHERE rd.id_ruta = ?";
                                                $stmtOrders = $conn->prepare($sqlOrders);
                                                $stmtOrders->bind_param("i", $route['route_id']);
                                                $stmtOrders->execute();
                                                $orders = $stmtOrders->get_result()->fetch_all(MYSQLI_ASSOC);
                                                $stmtOrders->close();

                                                if (count($orders) > 0) {
                                                    foreach ($orders as $order): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($order['shipment_id']); ?></td>
                                                            <td><?php echo htmlspecialchars($order['client_name']); ?></td>
                                                            <td><?php echo htmlspecialchars($order['street'] . ", " . $order['colony'] . " " . $order['number']); ?></td>
                                                            <td>
                                                                <form method="POST" class="d-inline">
                                                                    <input type="hidden" name="shipment_id" value="<?php echo htmlspecialchars($order['shipment_id']); ?>">
                                                                    <button type="submit" name="complete_order" class="btn btn-sm btn-outline-success">
                                                                        <i class="bi bi-check2"></i> Complete
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach;
                                                } else { ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No orders assigned to this route.</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>You have no assigned routes.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>



