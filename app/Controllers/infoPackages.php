<?php
session_start();
include '../../config/connection.php';

if (!isset($_GET['pedido_id'])) {
    echo "No se ha proporcionado un ID de pedido.";
    exit();
}

$pedido_id = $_GET['pedido_id'];

$sql_shipment = "SELECT * FROM Shipment WHERE num = ?";
$stmt_shipment = $conn->prepare($sql_shipment);
$stmt_shipment->bind_param("i", $pedido_id);
$stmt_shipment->execute();
$result_shipment = $stmt_shipment->get_result();
$shipment = $result_shipment->fetch_assoc();

if ($stmt_shipment->error) {
    echo "Error: " . $stmt_shipment->error;
}

$sql_record = "SELECT r.num, r.date, r.location, r.status
               FROM Record AS r
               WHERE r.shipment = ?";

$stmt_record = $conn->prepare($sql_record);
$stmt_record->bind_param("i", $pedido_id);
$stmt_record->execute();
$result_record = $stmt_record->get_result();
$records = $result_record->fetch_all(MYSQLI_ASSOC); 

if ($stmt_record->error) {
    echo "Error: " . $stmt_record->error;
}

$sql_insurance = "SELECT i.policyNumber, i.insurance_type, i.coverage
                  FROM Insurance AS i
                  JOIN Shipment AS s ON s.insurance = i.num
                  WHERE s.num = ?";

$stmt_insurance = $conn->prepare($sql_insurance);
$stmt_insurance->bind_param("i", $pedido_id);
$stmt_insurance->execute();
$result_insurance = $stmt_insurance->get_result();
$insurance = $result_insurance->fetch_assoc();

if ($stmt_insurance->error) {
    echo "Error: " . $stmt_insurance->error;
}

$sql_incidents = "SELECT i.num, i.description, i.date
                  FROM Incident AS i
                  JOIN Shipment AS s ON s.incident = i.num
                  WHERE s.num = ?";

$stmt_incidents = $conn->prepare($sql_incidents);
$stmt_incidents->bind_param("i", $pedido_id);
$stmt_incidents->execute();
$result_incidents = $stmt_incidents->get_result();
$incidents = $result_incidents->fetch_all(MYSQLI_ASSOC);

if ($stmt_incidents->error) {
    echo "Error: " . $stmt_incidents->error;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Pedido</title>
</head>
<body>
    <h1>Detalles del Pedido</h1>

    <h2>Información del Pedido</h2>
    <?php if ($shipment): ?>
        <p><strong>ID Pedido:</strong> <?php echo htmlspecialchars($shipment['num']); ?></p>
        <p><strong>Fecha del Pedido:</strong> <?php echo htmlspecialchars($shipment['date']); ?></p>
        <p><strong>Fecha de Entrega:</strong> <?php echo htmlspecialchars($shipment['delivery_date']); ?></p>
        <p><strong>ID del Cliente:</strong> <?php echo htmlspecialchars($shipment['client']); ?></p>
    <?php else: ?>
        <p>No se encontró información del pedido.</p>
    <?php endif; ?>

    <h2>Información del Registro</h2>
    <?php if ($records): ?>
        <table border="1">
            <tr>
                <th>ID Registro</th>
                <th>Fecha</th>
                <th>Ubicación</th>
                <th>Estado</th>
            </tr>
            <?php foreach ($records as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['num']); ?></td>
                    <td><?php echo htmlspecialchars($record['date']); ?></td>
                    <td><?php echo htmlspecialchars($record['location']); ?></td>
                    <td><?php echo htmlspecialchars($record['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No se encontró información del registro.</p>
    <?php endif; ?>

    <h2>Información del Seguro</h2>
    <?php if ($insurance): ?>
        <p><strong>Número de Póliza:</strong> <?php echo htmlspecialchars($insurance['policyNumber']); ?></p>
        <p><strong>Tipo de Seguro:</strong> <?php echo htmlspecialchars($insurance['insurance_type']); ?></p>
        <p><strong>Cobertura:</strong> <?php echo htmlspecialchars($insurance['coverage']); ?></p>
    <?php else: ?>
        <p>No se encontró información del seguro.</p>
    <?php endif; ?>

    <h2>Incidentes Asociados</h2>
    <?php if ($incidents): ?>
        <table border="1">
            <tr>
                <th>ID Incidente</th>
                <th>Descripción</th>
                <th>Fecha</th>
            </tr>
            <?php foreach ($incidents as $incident): ?>
                <tr>
                    <td><?php echo htmlspecialchars($incident['num']); ?></td>
                    <td><?php echo htmlspecialchars($incident['description']); ?></td>
                    <td><?php echo htmlspecialchars($incident['date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No se encontraron incidentes asociados a este pedido.</p>
    <?php endif; ?>
</body>
</html>


