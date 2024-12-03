<?php
include '../../config/connection.php'; 

function getStatusBadgeClass($status) {
    return match ($status) {
        'Order Placed' => 'info',
        'In Process' => 'warning',
        'In Transit' => 'primary',
        'Delivered' => 'success',
        default => 'secondary'
    };
}

$client_id = $_SESSION['client_id']; 

$sql_orders = "SELECT s.num, s.date, s.delivery_date, 
               (SELECT r.status 
                FROM Record r 
                WHERE r.shipment = s.num 
                ORDER BY r.date DESC 
                LIMIT 1) as status
               FROM Shipment AS s
               WHERE s.client = ?"; 

$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param("i", $client_id); 
$stmt_orders->execute(); 
$result_orders = $stmt_orders->get_result(); 
$orders = $result_orders->fetch_all(MYSQLI_ASSOC); 

if ($stmt_orders->error) {
    echo "Error: " . $stmt_orders->error; 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Información de los Pedidos</title>
</head>
<body>
    <div class="container py-4">
        <div class="container-fluid px-4">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">My Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Order Date</th>
                                            <th>Delivery Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($orders): ?>
                                            <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <td><span class="fw-bold">#<?php echo htmlspecialchars($order['num']); ?></span></td>
                                                    <td><?php echo date('d/m/Y', strtotime($order['date'])); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($order['delivery_date'])); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo getStatusBadgeClass($order['status']); ?>">
                                                            <?php echo htmlspecialchars($order['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button type="button" 
                                                                class="btn btn-outline-primary btn-sm"
                                                                onclick="loadTrackingInfo('<?php echo htmlspecialchars($order['num']); ?>')">
                                                            <i class="bi bi-geo-alt me-1"></i>Track
                                                        </button>
                                                    </td>
                                                </tr>
                                                <!-- Tracking details row -->
                                                <tr class="tracking-details" id="tracking-<?php echo htmlspecialchars($order['num']); ?>" style="display: none;">
                                                    <td colspan="5" class="p-0">
                                                        <div class="collapse" id="tracking-content-<?php echo htmlspecialchars($order['num']); ?>">
                                                            <div class="p-3 bg-body border-top">
                                                                <div class="tracking-info-content">
                                                                    <div class="text-center">
                                                                        <div class="spinner-border text-primary" role="status">
                                                                            <span class="visually-hidden">Loading...</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                        You don't have any orders yet.
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function loadTrackingInfo(trackingCode) {
        const trackingRow = document.getElementById(`tracking-${trackingCode}`);
        const trackingContent = document.getElementById(`tracking-content-${trackingCode}`);
        
        // Toggle visibility
        if (trackingRow.style.display === 'none') {
            trackingRow.style.display = 'table-row';
            trackingContent.classList.add('show');
            
            // Fetch tracking data
            fetch(`../../app/Controllers/trackingController.php?tracking_code=${trackingCode}`)
                .then(response => response.text())
                .then(data => {
                    const contentDiv = trackingContent.querySelector('.tracking-info-content');
                    contentDiv.innerHTML = data;
                })
                .catch(error => {
                    const contentDiv = trackingContent.querySelector('.tracking-info-content');
                    contentDiv.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Error loading tracking information.
                        </div>`;
                });
        } else {
            trackingRow.style.display = 'none';
            trackingContent.classList.remove('show');
        }
    }
    </script>

    <style>
    .tracking-details td {
        transition: all 0.3s ease;
    }

    .collapse {
        transition: all 0.3s ease;
    }

    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.8em;
    }

    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
    }

    .btn-outline-primary {
        transition: all 0.2s ease;
    }

    .btn-outline-primary:hover {
        transform: translateY(-1px);
    }
    </style>
</body>
</html>
