<?php
// Database connection and queries
require_once '../../config/connection.php';

// Total Employees (ALL)
$totalEmployeesQuery = "SELECT COUNT(*) as count FROM Employees WHERE role IN ('R001', 'R002', 'R003')";
$totalEmployeesResult = $conn->query($totalEmployeesQuery);
$totalEmployees = $totalEmployeesResult->fetch_assoc()['count'];

// Total Warehouses
$totalWarehousesQuery = "SELECT COUNT(*) as count FROM Warehouse";
$totalWarehousesResult = $conn->query($totalWarehousesQuery);
$totalWarehouses = $totalWarehousesResult->fetch_assoc()['count'];

// Total Vehicles
$totalVehiclesQuery = "SELECT COUNT(*) as count FROM Vehicle";
$totalVehiclesResult = $conn->query($totalVehiclesQuery);
$totalVehicles = $totalVehiclesResult->fetch_assoc()['count'];

// Successfully Delivered Packages
$deliveredPackagesQuery = "SELECT COUNT(*) as count FROM Record WHERE status = 'delivered'";
$deliveredPackagesResult = $conn->query($deliveredPackagesQuery);
$deliveredPackages = $deliveredPackagesResult->fetch_assoc()['count'];
?>

<div class="dashboard-welcome">
    <!-- Welcome Message -->
    <div class="welcome-message" data-aos="fade-up">
        <h2 class="mb-3">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
        <p class="mb-0">Here's what's happening with your logistics operations today.</p>
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
                <h6 class="text-muted">Total Warehouses</h6>
                <h3 class="mb-0"><?php echo $totalWarehouses; ?></h3>
                <small class="text-success">Storage Facilities</small>
            </div>
        </div>
        <div class="col-12 col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="stats-card">
                <h6 class="text-muted">Total Vehicles</h6>
                <h3 class="mb-0"><?php echo $totalVehicles; ?></h3>
                <small class="text-success">Transport Fleet</small>
            </div>
        </div>
        <div class="col-12 col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="stats-card">
                <h6 class="text-muted">Delivered Packages</h6>
                <h3 class="mb-0"><?php echo $deliveredPackages; ?></h3>
                <small class="text-success">Successfully Completed</small>
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