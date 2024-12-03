<?php
include '../../config/connection.php';
$supervisor_id = $_SESSION['num'] ?? null;
$warehouse_code = null;
$warehouse_name = null;

if ($supervisor_id) {
    $query = "SELECT W.code AS warehouse_code, W.name AS warehouse_name 
              FROM Employees E
              JOIN Warehouse W ON E.warehouse = W.code
              WHERE E.num = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $supervisor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $warehouse_code = $row['warehouse_code'];
        $warehouse_name = $row['warehouse_name'];
    } else {
        die("Error: No se encontró un almacén para el supervisor.");
    }
} else {
    die("Error: No se pudo identificar al supervisor.");
}

$stockQuery = "SELECT code, name, amount FROM Stock WHERE warehouse = ?";
$stockResult = $conn->prepare($stockQuery);
$stockResult->bind_param("s", $warehouse_code);
$stockResult->execute();
$stockData = $stockResult->get_result();

$insuranceQuery = "SELECT num, policyNumber, insurance_type FROM Insurance";
$clientQuery = "SELECT num, name, lastname FROM Client";

$insuranceResult = $conn->query($insuranceQuery);
$clientResult = $conn->query($clientQuery);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Paquetes y Pedidos</title>
</head>
<body>
    <div class="container mt-5">
        <div class="card package-form-card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Create Package and Order</h2>
            </div>
            <div class="card-body">
                <!-- Alerts section -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success fade show" role="alert">
                        <?= htmlspecialchars($_GET['success']) ?>
                    </div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger fade show" role="alert">
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="../../app/Controllers/newPackage.php" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="client" class="form-label">Select Client:</label>
                            <select name="client" class="form-select custom-select" required>
                                <?php while ($client = $clientResult->fetch_assoc()): ?>
                                    <option value="<?= $client['num'] ?>"><?= $client['name'] . " " . $client['lastname'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="insurance" class="form-label">Select Insurance:</label>
                            <select name="insurance" class="form-select custom-select" required>
                                <?php while ($insurance = $insuranceResult->fetch_assoc()): ?>
                                    <option value="<?= $insurance['num'] ?>">
                                        <?= $insurance['policyNumber'] . " - " . $insurance['insurance_type'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                       <div class="col-md-6 mb-3">
                        <label for="shipment_date" class="form-label">Shipment Date:</label>
                        <input type="date" name="shipment_date" class="form-control custom-date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                      <div class="col-md-6 mb-3">
                        <label for="delivery_date" class="form-label">Delivery Date:</label>
                        <input type="date" name="delivery_date" class="form-control custom-date" required min="<?php echo date('Y-m-d'); ?>">
                     </div>
                    </div>

                    <div class="warehouse-products mt-4">
                        <h3 class="mb-3 border-bottom pb-2">Warehouse Products</h3>
                        <div class="row row-cols-1 row-cols-md-2 g-4">
                            <?php while ($stock = $stockData->fetch_assoc()): ?>
                                <div class="col">
                                    <div class="product-item card h-100">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="item[]" value="<?= $stock['code'] ?>" 
                                                       id="item-<?= $stock['code'] ?>">
                                                <label class="form-check-label" for="item-<?= $stock['code'] ?>">
                                                    <?= $stock['name'] ?>
                                                </label>
                                            </div>
                                            <div class="quantity-input mt-2">
                                                <small class="text-muted">Available: <?= $stock['amount'] ?></small>
                                                <input type="number" name="quantity[]" 
                                                       class="form-control form-control-sm mt-1" 
                                                       min="1" max="<?= $stock['amount'] ?>" 
                                                       step="1" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Create Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const quantityInput = this.closest('.card-body').querySelector('input[type="number"]');
                if (quantityInput) {
                    quantityInput.disabled = !this.checked;
                    if (!this.checked) {
                        quantityInput.value = '';
                    }
                }
            });
        });
    </script>
</body>
</html>
