<?php

include '../../config/connection.php';

$client_id = $_SESSION['client_id'];

// Get client information
$sqlClient = "SELECT * FROM Client WHERE num = ?";
$stmtClient = $conn->prepare($sqlClient);
$stmtClient->bind_param("i", $client_id);
$stmtClient->execute();
$client = $stmtClient->get_result()->fetch_assoc();
$stmtClient->close();

// Initialize default values
$activeShipments = ['active_count' => 0];
$totalShipments = ['total_count' => 0];
$pendingDeliveries = ['pending_count' => 0];

// Only proceed with queries if we have a client
if ($client) {
    // Get active shipments count (In Transit or In Process)
    $sqlActiveShipments = "SELECT COUNT(DISTINCT s.num) as active_count
        FROM Shipment s
        JOIN Record r ON s.num = r.shipment
        WHERE s.client = ? 
        AND r.status IN ('In Transit', 'In Process')
        AND r.date = (
            SELECT MAX(date)
            FROM Record r2
            WHERE r2.shipment = s.num
        )";
    $stmtActiveShipments = $conn->prepare($sqlActiveShipments);
    $stmtActiveShipments->bind_param("i", $client_id);
    $stmtActiveShipments->execute();
    $activeShipments = $stmtActiveShipments->get_result()->fetch_assoc();
    $stmtActiveShipments->close();

    // Get total shipments count
    $sqlTotalShipments = "SELECT COUNT(DISTINCT num) as total_count
        FROM Shipment 
        WHERE client = ?";
    $stmtTotalShipments = $conn->prepare($sqlTotalShipments);
    $stmtTotalShipments->bind_param("i", $client_id);
    $stmtTotalShipments->execute();
    $totalShipments = $stmtTotalShipments->get_result()->fetch_assoc();
    $stmtTotalShipments->close();

    // Get pending deliveries (Order Placed status)
    $sqlPendingDeliveries = "SELECT COUNT(DISTINCT s.num) as pending_count
        FROM Shipment s
        JOIN Record r ON s.num = r.shipment
        WHERE s.client = ? 
        AND r.status = 'Order Placed'
        AND r.date = (
            SELECT MAX(date)
            FROM Record r2
            WHERE r2.shipment = s.num
        )";
    $stmtPendingDeliveries = $conn->prepare($sqlPendingDeliveries);
    $stmtPendingDeliveries->bind_param("i", $client_id);
    $stmtPendingDeliveries->execute();
    $pendingDeliveries = $stmtPendingDeliveries->get_result()->fetch_assoc();
    $stmtPendingDeliveries->close();
}
?>

<div class="container-fluid px-4 py-5">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="display-6">Welcome back<?php echo $client ? ', ' . htmlspecialchars($client['name']) : ''; ?>!</h2>
                            <p class="lead mb-0">Today is <?php echo date('l, F j, Y'); ?></p>
                        </div>
                        <i class="bi bi-box-seam fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Active Shipments -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Active Shipments</h6>
                            <h2 class="card-title mb-0"><?php echo isset($activeShipments['active_count']) ? $activeShipments['active_count'] : '0'; ?></h2>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="bi bi-truck fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Shipments -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Total Shipments</h6>
                            <h2 class="card-title mb-0"><?php echo isset($totalShipments['total_count']) ? $totalShipments['total_count'] : '0'; ?></h2>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="bi bi-box fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Pending Orders</h6>
                            <h2 class="card-title mb-0"><?php echo isset($pendingDeliveries['pending_count']) ? $pendingDeliveries['pending_count'] : '0'; ?></h2>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="bi bi-clock-history fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Quick Actions</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="?page=packages" class="btn btn-outline-primary w-100 p-3">
                                <i class="bi bi-box me-2"></i>View My Packages
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="?page=trackPackage" class="btn btn-outline-success w-100 p-3">
                                <i class="bi bi-geo-alt me-2"></i>Track Package
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="?page=reportIncident" class="btn btn-outline-warning w-100 p-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>Report Issue
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Shipments -->
    <?php if ($client): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Recent Shipments</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Shipment #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sqlRecentShipments = "SELECT s.num, s.date, r.status 
                                    FROM Shipment s
                                    LEFT JOIN Record r ON s.num = r.shipment
                                    WHERE s.client = ?
                                    AND r.date = (
                                        SELECT MAX(date)
                                        FROM Record r2
                                        WHERE r2.shipment = s.num
                                    )
                                    ORDER BY s.date DESC LIMIT 5";
                                $stmtRecentShipments = $conn->prepare($sqlRecentShipments);
                                $stmtRecentShipments->bind_param("i", $client_id);
                                $stmtRecentShipments->execute();
                                $recentShipments = $stmtRecentShipments->get_result();
                                
                                while ($shipment = $recentShipments->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($shipment['num']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($shipment['date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo match($shipment['status']) {
                                                    'Order Delivered' => 'success',
                                                    'In Transit' => 'primary',
                                                    'Order Placed' => 'info',
                                                    default => 'secondary'
                                                };
                                            ?>">
                                                <?php echo htmlspecialchars($shipment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="?page=trackPackage&shipment=<?php echo $shipment['num']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                Track
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile;
                                $stmtRecentShipments->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
