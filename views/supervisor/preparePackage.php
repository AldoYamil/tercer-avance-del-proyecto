<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../config/connection.php';

$supervisor_id = $_SESSION['num'];

$sql = "SELECT warehouse FROM Employees WHERE num = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supervisor_id);
$stmt->execute();
$stmt->bind_result($warehouse_code);
$stmt->fetch();
$stmt->close();

$sqlOperators = "SELECT emp.num AS employee_id, emp.name, emp.lastname, emp.surname 
                 FROM Employees emp
                 JOIN Role rol ON emp.role = rol.code
                 WHERE rol.code = 'R002' AND emp.warehouse = ?";
$stmtOperators = $conn->prepare($sqlOperators);
$stmtOperators->bind_param("s", $warehouse_code);
$stmtOperators->execute();
$operators = $stmtOperators->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtOperators->close();

$sqlOrders = "SELECT sh.num AS shipment_id, sh.date, cl.street, cl.colony, cl.number
              FROM Shipment sh
              JOIN Client cl ON sh.client = cl.num
              JOIN Record rec ON rec.shipment = sh.num
              WHERE rec.status = 'Order Placed' AND sh.vehicle IS NULL AND sh.path IS NULL
              AND sh.warehouse = ? 
              AND NOT EXISTS (
                  SELECT 1 FROM Record rec2 
                  WHERE rec2.shipment = sh.num 
                  AND rec2.status != 'Order Placed'
              )";
$stmtOrders = $conn->prepare($sqlOrders);
$stmtOrders->bind_param("s", $warehouse_code);
$stmtOrders->execute();
$orders = $stmtOrders->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtOrders->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employee_id']) && isset($_POST['shipment_id'])) {
    $employee_id = $_POST['employee_id'];
    $shipment_id = $_POST['shipment_id'];

    $sqlAssign = "INSERT INTO Assamble (employees, shipment, status) VALUES (?, ?, 'Earring')";
    $stmtAssign = $conn->prepare($sqlAssign);
    $stmtAssign->bind_param("ii", $employee_id, $shipment_id);

    if ($stmtAssign->execute()) {
        $sqlUpdateStatus = "INSERT INTO Record (date, location, status, client, shipment)
                            VALUES (CURDATE(), 
                                    (SELECT CONCAT(w.street, ', ', w.colony, ' ', w.number) 
                                     FROM Warehouse w 
                                     JOIN Employees e ON e.warehouse = w.code 
                                     WHERE e.num = ?), 
                                    'In Process', 
                                    (SELECT client FROM Shipment WHERE num = ?), ?)";
        $stmtUpdateStatus = $conn->prepare($sqlUpdateStatus);
        $stmtUpdateStatus->bind_param("iii", $employee_id, $shipment_id, $shipment_id);
        
        if ($stmtUpdateStatus->execute()) {
            echo "El pedido ha sido asignado al empleado con estatus 'Earring' y el registro ha cambiado a 'In Process' con la ubicación del almacén del operador.";
        } else {
            echo "Error al actualizar el estatus del pedido: " . $stmtUpdateStatus->error;
        }
        $stmtUpdateStatus->close();
    } else {
        echo "Error al asignar el pedido al empleado: " . $stmtAssign->error;
    }
    $stmtAssign->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asignar Pedido a Empleado Operador</title>
</head>
<body class="bg-body">
    <div class="container mt-4">
        <h2 class="mb-4 text-center">Asignar Pedido a Empleado Operador</h2>
        
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-4">
                        <h3 class="h5 mb-3">Seleccione un Empleado Operador</h3>
                        <select name="employee_id" class="form-select" required>
                            <?php foreach ($operators as $operator): ?>
                                <option value="<?php echo $operator['employee_id']; ?>">
                                    <?php echo $operator['name'] . " " . $operator['lastname'] . " " . $operator['surname']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <h3 class="h5 mb-3">Seleccione un Pedido para Asignar</h3>
                        <select name="shipment_id" class="form-select" required>
                            <?php foreach ($orders as $order): ?>
                                <option value="<?php echo $order['shipment_id']; ?>">
                                    <?php echo "Pedido ID: " . $order['shipment_id'] . " - Dirección: " . $order['street'] . ", " . $order['colony'] . " " . $order['number']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Asignar Pedido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
