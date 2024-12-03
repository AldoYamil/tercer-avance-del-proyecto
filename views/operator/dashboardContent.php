<?php

include '../../config/connection.php';

$employee_num = $_SESSION['num'];

// Get employee information
$sqlEmployee = "SELECT * FROM Employees WHERE num = ?";
$stmtEmployee = $conn->prepare($sqlEmployee);
$stmtEmployee->bind_param("i", $employee_num);
$stmtEmployee->execute();
$employee = $stmtEmployee->get_result()->fetch_assoc();
$stmtEmployee->close();

// Initialize default values
$assignedPackages = ['package_count' => 0];
$lowStockItems = ['low_stock_count' => 0];
$reportedIncidents = ['incident_count' => 0];

// Only proceed with queries if we have an employee
if ($employee) {
    // Get assigned packages count
    $sqlAssignedPackages = "SELECT COUNT(*) as package_count
        FROM Assamble a
        WHERE a.employees = ? AND a.status != 'Finished'";
    $stmtAssignedPackages = $conn->prepare($sqlAssignedPackages);
    $stmtAssignedPackages->bind_param("i", $employee_num);
    $stmtAssignedPackages->execute();
    $result = $stmtAssignedPackages->get_result();
    if ($row = $result->fetch_assoc()) {
        $assignedPackages = $row;
    }
    $stmtAssignedPackages->close();

    // Get low stock items count (items with less than 1000 units)
    $sqlLowStock = "SELECT COUNT(*) as low_stock_count
        FROM Stock 
        WHERE amount < 1000";
    $stmtLowStock = $conn->prepare($sqlLowStock);
    $stmtLowStock->execute();
    $result = $stmtLowStock->get_result();
    if ($row = $result->fetch_assoc()) {
        $lowStockItems = $row;
    }
    $stmtLowStock->close();

    // Get reported incidents count
    $sqlIncidents = "SELECT COUNT(*) as incident_count
        FROM Incident 
        WHERE status = 'open'";
    $stmtIncidents = $conn->prepare($sqlIncidents);
    $stmtIncidents->execute();
    $result = $stmtIncidents->get_result();
    if ($row = $result->fetch_assoc()) {
        $reportedIncidents = $row;
    }
    $stmtIncidents->close();
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
                            <h2 class="display-6">Welcome back<?php echo $employee ? ', ' . htmlspecialchars($employee['name']) : ''; ?>!</h2>
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
        <!-- Assigned Packages -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Pending Packages</h6>
                            <h2 class="card-title mb-0"><?php echo isset($assignedPackages['package_count']) ? $assignedPackages['package_count'] : '0'; ?></h2>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="bi bi-box-seam fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Low Stock Items</h6>
                            <h2 class="card-title mb-0"><?php echo isset($lowStockItems['low_stock_count']) ? $lowStockItems['low_stock_count'] : '0'; ?></h2>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="bi bi-exclamation-triangle fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reported Incidents -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Open Incidents</h6>
                            <h2 class="card-title mb-0"><?php echo isset($reportedIncidents['incident_count']) ? $reportedIncidents['incident_count'] : '0'; ?></h2>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="bi bi-shield-exclamation fs-4 text-danger"></i>
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
                            <a href="?page=assignedPackage" class="btn btn-outline-primary w-100 p-3">
                                <i class="bi bi-box-seam me-2"></i>View Assigned Packages
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="?page=reportIncident" class="btn btn-outline-warning w-100 p-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>Report Incident
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="?page=profileInfo" class="btn btn-outline-info w-100 p-3">
                                <i class="bi bi-person me-2"></i>View Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
