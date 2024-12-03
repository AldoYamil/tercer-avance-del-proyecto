<?php
// Database connection and queries
require_once '../../config/connection.php';

// Get supervisor's warehouse from employees table using session user
$supervisorQuery = "SELECT e.warehouse, w.name as warehouse_name 
                   FROM Employees e 
                   JOIN Warehouse w ON e.warehouse = w.code 
                   JOIN Users u ON e.usernum = u.num 
                   WHERE u.username = ?";
$stmt = $conn->prepare($supervisorQuery);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$supervisorData = $result->fetch_assoc();
$warehouseCode = $supervisorData['warehouse'];
$warehouseName = $supervisorData['warehouse_name'];

// Total Employees in this warehouse
$totalEmployeesQuery = "SELECT COUNT(*) as count FROM Employees 
                       WHERE warehouse = ? AND role IN ('R001', 'R002', 'R003')";
$stmt = $conn->prepare($totalEmployeesQuery);
$stmt->bind_param("s", $warehouseCode);
$stmt->execute();
$totalEmployees = $stmt->get_result()->fetch_assoc()['count'];

// Total Vehicles in this warehouse
$totalVehiclesQuery = "SELECT COUNT(*) as count FROM Vehicle WHERE warehouse = ?";
$stmt = $conn->prepare($totalVehiclesQuery);
$stmt->bind_param("s", $warehouseCode);
$stmt->execute();
$totalVehicles = $stmt->get_result()->fetch_assoc()['count'];

// Successfully Delivered Packages from this warehouse
$deliveredPackagesQuery = "SELECT COUNT(*) as count FROM Record r 
                          JOIN Shipment s ON r.shipment = s.num 
                          WHERE s.warehouse = ? AND r.status = 'delivered'";
$stmt = $conn->prepare($deliveredPackagesQuery);
$stmt->bind_param("s", $warehouseCode);
$stmt->execute();
$deliveredPackages = $stmt->get_result()->fetch_assoc()['count'];

// Total Incidents for this warehouse
$totalIncidentsQuery = "SELECT COUNT(*) as count FROM Incident i 
                       JOIN Users u ON i.user = u.num 
                       JOIN Employees e ON u.num = e.usernum 
                       WHERE e.warehouse = ?";
$stmt = $conn->prepare($totalIncidentsQuery);
$stmt->bind_param("s", $warehouseCode);
$stmt->execute();
$totalIncidents = $stmt->get_result()->fetch_assoc()['count'];
?>

<div class="dashboard-welcome">
    <!-- Welcome Message -->
    <div class="welcome-message" data-aos="fade-up">
        <h2 class="mb-3">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
        <p class="mb-0">Managing <?php echo htmlspecialchars($warehouseName); ?></p>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats row g-3 mb-4">
        <div class="col-12 col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="stats-card">
                <h6 class="text-muted">Total Employees</h6>
                <h3 class="mb-0"><?php echo $totalEmployees; ?></h3>
                <small class="text-success">Active Personnel</small>
            </div>
        </div>
        <div class="col-12 col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="stats-card">
                <h6 class="text-muted">Total Vehicles</h6>
                <h3 class="mb-0"><?php echo $totalVehicles; ?></h3>
                <small class="text-success">Transport Fleet</small>
            </div>
        </div>
        <div class="col-12 col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stats-card">
                <h6 class="text-muted">Delivered Packages</h6>
                <h3 class="mb-0"><?php echo $deliveredPackages; ?></h3>
                <small class="text-success">Successfully Completed</small>
            </div>
        </div>
        <div class="col-12 col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stats-card">
                <h6 class="text-muted">Total Reports</h6>
                <h3 class="mb-0"><?php echo $totalIncidents; ?></h3>
                <small class="text-warning">Incidents Logged</small>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <h4 class="mb-3">Quick Actions</h4>
    <div class="quick-links-grid">
        <div class="card quick-link-card" data-aos="fade-up" data-aos-delay="100">
            <div class="d-flex align-items-center">
                <i class="bi bi-box fs-1 me-3 text-primary"></i>
                <div>
                    <h5 class="mb-1">Manage Stock</h5>
                    <p class="mb-0 text-muted">Update inventory and check stock levels</p>
                </div>
            </div>
        </div>
        
        <div class="card quick-link-card" data-aos="fade-up" data-aos-delay="200">
            <div class="d-flex align-items-center">
                <i class="bi bi-cart3 fs-1 me-3 text-success"></i>
                <div>
                    <h5 class="mb-1">New Order</h5>
                    <p class="mb-0 text-muted">Process a new customer order</p>
                </div>
            </div>
        </div>
        
        <div class="card quick-link-card" data-aos="fade-up" data-aos-delay="300">
            <div class="d-flex align-items-center">
                <i class="bi bi-file-earmark-text fs-1 me-3 text-warning"></i>
                <div>
                    <h5 class="mb-1">Generate Report</h5>
                    <p class="mb-0 text-muted">Create sales and inventory reports</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Getting Started Guide -->
    <div class="card mt-4" data-aos="fade-up" data-aos-delay="400">
        <div class="card-body">
            <h4 class="card-title">Getting Started</h4>
            <p class="card-text">Here's how to make the most of your dashboard:</p>
            <ul class="list-unstyled">
                <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Use the sidebar to navigate between different sections</li>
                <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Monitor your stock levels and set up alerts</li>
                <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Process orders and manage customer information</li>
                <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i> Generate reports for business insights</li>
            </ul>
        </div>
    </div>
</div> 