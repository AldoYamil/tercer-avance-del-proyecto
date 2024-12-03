<?php
include '../../config/connection.php';

$employeeNum = $_SESSION['num'];

$sqlEmpleado = "SELECT name, lastname, surname FROM Employees WHERE num = ?";
$stmtEmpleado = $conn->prepare($sqlEmpleado);
$stmtEmpleado->bind_param("i", $employeeNum);
$stmtEmpleado->execute();
$stmtEmpleado->bind_result($employeeName, $employeeLastname, $employeeSurname);
$stmtEmpleado->fetch();
$stmtEmpleado->close();

if (!$employeeName) {
    die("Error: No se pudo obtener los detalles del empleado.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shipment_id'])) {
    $shipmentId = $_POST['shipment_id'];

    $sqlUpdateStatus = "UPDATE Assamble SET status = 'Finished' WHERE shipment = ?";
    $stmtUpdate = $conn->prepare($sqlUpdateStatus);
    $stmtUpdate->bind_param("i", $shipmentId);
    $stmtUpdate->execute();
    $stmtUpdate->close();
}

$sql = "SELECT s.num AS shipment_id, s.date AS shipment_date, s.delivery_date, c.name AS client_name, a.status
        FROM Assamble a
        JOIN Shipment s ON a.shipment = s.num
        JOIN Client c ON s.client = c.num
        WHERE a.employees = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employeeNum);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Orders</title>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Orders Assigned to <?php echo htmlspecialchars($employeeName . ' ' . $employeeLastname . ' ' . $employeeSurname); ?></h2>
                <p class="text-muted">These are your assigned orders:</p>

                <?php if ($result->num_rows > 0): ?>
                    <div class="row">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Order #<?php echo htmlspecialchars($row['shipment_id']); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Order Date:</strong> <?php echo htmlspecialchars($row['shipment_date']); ?><br>
                                        <strong>Delivery Date:</strong> <?php echo htmlspecialchars($row['delivery_date']); ?><br>
                                        <strong>Client:</strong> <?php echo htmlspecialchars($row['client_name']); ?>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Package Contents:</strong>
                                        <?php
                                        $sqlContenido = "SELECT s.name AS item_name, p.amount
                                                         FROM Package p
                                                         JOIN Stock s ON p.stock = s.code  
                                                         WHERE p.shipment = ?";
                                        $stmtContenido = $conn->prepare($sqlContenido);
                                        $stmtContenido->bind_param("i", $row['shipment_id']);
                                        $stmtContenido->execute();
                                        $contenido = $stmtContenido->get_result()->fetch_all(MYSQLI_ASSOC);
                                        $stmtContenido->close();
                                        ?>

                                        <ul class="list-group list-group-flush mt-2">
                                        <?php foreach ($contenido as $item): ?>
                                            <li class="list-group-item">
                                                <?php echo htmlspecialchars($item['item_name']); ?>
                                                <span class="badge bg-secondary float-end">
                                                    <?php echo htmlspecialchars($item['amount']); ?>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                        </ul>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge <?php echo ($row['status'] === 'Earring') ? 'bg-warning' : 'bg-success'; ?> p-2">
                                            <?php echo ($row['status'] === 'Earring') ? 'Pending' : 'Completed'; ?>
                                        </span>

                                        <?php if ($row['status'] === 'Earring'): ?>
                                            <form method="post">
                                                <input type="hidden" name="shipment_id" value="<?php echo $row['shipment_id']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="bi bi-check-circle me-1"></i>Mark as Completed
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>You don't have any assigned orders.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
