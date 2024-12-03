<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'R003') {
    header("Location: ../auth/LoginViewEmployee.php");
    exit();
}

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
$todayRoutes = ['route_count' => 0];
$pendingDeliveries = ['pending_count' => 0];
$vehicle = null;

// Only proceed with queries if we have an employee
if ($employee) {
    // Get today's routes count
    $sqlTodayRoutes = "SELECT COUNT(DISTINCT p.num) as route_count
        FROM RutaDetalles rd
        JOIN Path p ON rd.id_ruta = p.num
        JOIN Vehicle v ON rd.id_vehiculo = v.number
        WHERE v.number IN (
            SELECT vehicle_number 
            FROM Vehicle_Assignment 
            WHERE employee_num = ?
        ) AND DATE(p.starting_date) = CURDATE()";
    $stmtTodayRoutes = $conn->prepare($sqlTodayRoutes);
    $stmtTodayRoutes->bind_param("i", $employee_num);
    $stmtTodayRoutes->execute();
    $result = $stmtTodayRoutes->get_result();
    if ($row = $result->fetch_assoc()) {
        $todayRoutes = $row;
    }
    $stmtTodayRoutes->close();

    // Get pending deliveries count
    $sqlPendingDeliveries = "SELECT COUNT(*) as pending_count
        FROM Shipment s
        JOIN RutaDetalles rd ON rd.id_paquete = s.num
        JOIN Vehicle v ON rd.id_vehiculo = v.number
        WHERE v.number IN (
            SELECT vehicle_number 
            FROM Vehicle_Assignment 
            WHERE employee_num = ?
        ) AND s.num NOT IN (
            SELECT shipment FROM Record WHERE status = 'Order Delivered'
        )";
    $stmtPendingDeliveries = $conn->prepare($sqlPendingDeliveries);
    $stmtPendingDeliveries->bind_param("i", $employee_num);
    $stmtPendingDeliveries->execute();
    $result = $stmtPendingDeliveries->get_result();
    if ($row = $result->fetch_assoc()) {
        $pendingDeliveries = $row;
    }
    $stmtPendingDeliveries->close();

    // Get assigned vehicle information
    $sqlVehicle = "SELECT v.* 
        FROM Vehicle v
        JOIN Vehicle_Assignment va ON v.number = va.vehicle_number
        WHERE va.employee_num = ?";
    $stmtVehicle = $conn->prepare($sqlVehicle);
    $stmtVehicle->bind_param("i", $employee_num);
    $stmtVehicle->execute();
    $vehicle = $stmtVehicle->get_result()->fetch_assoc();
    $stmtVehicle->close();
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
                        <i class="bi bi-truck fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Today's Routes -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Today's Routes</h6>
                            <h2 class="card-title mb-0"><?php echo isset($todayRoutes['route_count']) ? $todayRoutes['route_count'] : '0'; ?></h2>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="bi bi-map fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Deliveries -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Pending Deliveries</h6>
                            <h2 class="card-title mb-0"><?php echo isset($pendingDeliveries['pending_count']) ? $pendingDeliveries['pending_count'] : '0'; ?></h2>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="bi bi-box-seam fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Vehicle -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Assigned Vehicle</h6>
                            <h2 class="card-title mb-0"><?php echo $vehicle ? htmlspecialchars($vehicle['license_plate']) : 'N/A'; ?></h2>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="bi bi-truck fs-4 text-success"></i>
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
                            <a href="?page=infoPath" class="btn btn-outline-primary w-100 p-3">
                                <i class="bi bi-map me-2"></i>View Route Information
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

    <!-- Vehicle Information -->
    <?php if ($vehicle): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Vehicle Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th>License Plate:</th>
                                    <td><?php echo htmlspecialchars($vehicle['license_plate']); ?></td>
                                </tr>
                                <tr>
                                    <th>Model:</th>
                                    <td><?php echo htmlspecialchars($vehicle['model']); ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-<?php echo $vehicle['status'] == 'available' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst(htmlspecialchars($vehicle['status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Remember to check your vehicle condition before starting your route!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
