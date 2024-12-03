<?php
include '../../config/connection.php';

// Obtener el almacén del supervisor desde la sesión
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
    }
}

// Consulta corregida para incluir información del vehículo desde RutaDetalles
$sql = "SELECT 
        P.num AS id_ruta,
        P.starting_point,
        P.end_point,
        P.est_arrival,
        P.starting_date,
        RD.id_vehiculo AS vehicle_id,
        V.license_plate,
        V.model AS vehicle_model
    FROM Path P
    LEFT JOIN RutaDetalles RD ON P.num = RD.id_ruta
    LEFT JOIN Vehicle V ON RD.id_vehiculo = V.number
    WHERE P.starting_point = ?
    ORDER BY P.starting_date DESC";

// Consulta de empleados disponibles
$sqlEmployees = "SELECT num, name, lastname 
                 FROM Employees 
                 WHERE role = 'R003' 
                 AND warehouse = ? 
                 AND status = 'available'";

// Consulta de vehículos disponibles
$sqlVehicles = "SELECT number, license_plate, model 
                FROM Vehicle 
                WHERE warehouse = ? 
                AND status = 'available'";
// Obtener paquetes disponibles para el modal
$sqlPackages = "SELECT DISTINCT S.num, CONCAT(C.street, ' ', C.number, ', ', C.colony) AS destination_address
                FROM Record R
                JOIN Shipment S ON R.shipment = S.num
                JOIN Client C ON S.client = C.num
                WHERE S.warehouse IN (SELECT code FROM Warehouse WHERE name = ?) 
                AND S.vehicle IS NULL AND S.path IS NULL
                GROUP BY R.shipment
                HAVING COUNT(DISTINCT R.status) = 2
                ORDER BY S.num ASC";

// Preparar y ejecutar consultas
$stmtPackages = $conn->prepare($sqlPackages);
$stmtPackages->bind_param("s", $warehouse_name);
$stmtPackages->execute();
$availablePackages = $stmtPackages->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtPackages->close();

$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>

<div class="container-fluid py-4">
    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Path Management</h1>
        <div class="d-flex gap-2">
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control" placeholder="Search paths...">
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPathModal">
                <i class="bi bi-plus-lg"></i> New Path
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Route ID <i class="bi bi-arrow-down-up sort-icon" data-column="id_ruta"></i></th>
                            <th>Starting Date <i class="bi bi-arrow-down-up sort-icon" data-column="starting_date"></i></th>
                            <th>Est. Arrival <i class="bi bi-arrow-down-up sort-icon" data-column="est_arrival"></i></th>
                            <th>End Point</th>
                            <th>Vehicle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $warehouse_code);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        while ($path = $result->fetch_assoc()):
                            $pathData = htmlspecialchars(json_encode($path), ENT_QUOTES, 'UTF-8');
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($path['id_ruta']); ?></td>
                                <td><?php echo htmlspecialchars($path['starting_date']); ?></td>
                                <td><?php echo htmlspecialchars($path['est_arrival']); ?></td>
                                <td><?php echo htmlspecialchars($path['end_point']); ?></td>
                                <td>
                                    <?php 
                                    echo $path['license_plate'] 
                                        ? htmlspecialchars($path['license_plate'] . ' (' . $path['vehicle_model'] . ')') 
                                        : 'Not assigned'; 
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info view-path" data-bs-toggle="modal" data-bs-target="#viewPathModal" data-path="<?php echo $pathData; ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-path" data-route="<?php echo isset($path['id_ruta']) ? htmlspecialchars($path['id_ruta']) : ''; ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <!-- Create Path Modal -->
    <div class="modal fade" id="createPathModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Path</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="../../app/Controllers/createPath.php" method="POST">
                        <input type="hidden" name="warehouse_code" value="<?php echo htmlspecialchars($warehouse_code); ?>">
                        
                        <div class="mb-3">
                        <label class="form-label">Select Employee</label>
                        <select name="employee_num" class="form-select" required>
                            <option value="">Choose an employee...</option>
                            <?php
                            $stmt = $conn->prepare($sqlEmployees);
                            $stmt->bind_param("s", $warehouse_code);
                            $stmt->execute();
                            $employees = $stmt->get_result();
                            while ($emp = $employees->fetch_assoc()):
                            ?>
                                <option value="<?php echo $emp['num']; ?>">
                                    <?php echo htmlspecialchars($emp['name'] . ' ' . $emp['lastname']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Vehicle</label>
                        <select name="vehicle_number" class="form-select" required>
                            <option value="">Choose a vehicle...</option>
                            <?php
                            $stmt = $conn->prepare($sqlVehicles);
                            $stmt->bind_param("s", $warehouse_code);
                            $stmt->execute();
                            $vehicles = $stmt->get_result();
                            while ($vehicle = $vehicles->fetch_assoc()):
                            ?>
                                <option value="<?php echo $vehicle['number']; ?>">
                                    <?php echo htmlspecialchars($vehicle['license_plate'] . ' (' . $vehicle['model'] . ')'); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                        <!-- Package Selection -->
                        <div class="mb-4">
                        <h3 class="h5 mb-3">Available Packages</h3>
                        <?php if (!empty($availablePackages)): ?>
                            <div class="row row-cols-1 row-cols-md-2 g-4">
                                <?php foreach ($availablePackages as $package): ?>
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="packages[]" 
                                                   value="<?php echo $package['num']; ?>" id="package<?php echo $package['num']; ?>">
                                            <label class="form-check-label" for="package<?php echo $package['num']; ?>">
                                                Package <?php echo $package['num']; ?>
                                                <small class="d-block text-muted">
                                                    <?php echo $package['destination_address']; ?>
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">No available packages.</div>
                        <?php endif; ?>
                    </div>
                        </div>

                        <!-- Dates -->
                         <div class="row">
                            <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Starting Date</label>
                                <input type="date" name="starting_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        </div>
                            <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Estimated Arrival</label>
                                <input type="date" name="arrival_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        </div>
                    </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create Path</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Path Modal -->
<div class="modal fade" id="viewPathModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Path Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="pathDetails"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('tbody');
    const searchInput = document.getElementById('searchInput');
    const sortIcons = document.querySelectorAll('.sort-icon');
    let tableData = [];
    let sortDirection = {};

    // Collect table data
    function collectTableData() {
        tableData = [];
        const rows = tableBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            tableData.push({
                element: row,
                id_ruta: cells[0].textContent,
                starting_date: cells[1].textContent,
                est_arrival: cells[2].textContent,
                searchText: Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ')
            });
        });
    }

    // Filter table
    function filterTable(searchTerm) {
        searchTerm = searchTerm.toLowerCase();
        tableData.forEach(row => {
            const show = row.searchText.includes(searchTerm);
            row.element.style.display = show ? '' : 'none';
        });
    }

    // Sort table
    function sortTable(column) {
        sortDirection[column] = sortDirection[column] === 'asc' ? 'desc' : 'asc';
        
        tableData.sort((a, b) => {
            let comparison = 0;
            if (a[column] < b[column]) comparison = -1;
            if (a[column] > b[column]) comparison = 1;
            return sortDirection[column] === 'asc' ? comparison : -comparison;
        });

        tableData.forEach(row => {
            tableBody.appendChild(row.element);
        });
    }

    // Event Listeners
    searchInput.addEventListener('input', (e) => {
        filterTable(e.target.value);
    });

    sortIcons.forEach(icon => {
        icon.addEventListener('click', () => {
            const column = icon.dataset.column;
            sortTable(column);
        });
    });

    // View path details
    const viewButtons = document.querySelectorAll('.view-path');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const pathData = JSON.parse(this.dataset.path);
            // Aquí puedes personalizar cómo se muestran los detalles
            document.getElementById('pathDetails').innerHTML = `
                <div class="mb-3">
                    <h6>Route ID: ${pathData.id_ruta}</h6>
                    <p>Starting Point: ${pathData.starting_point}</p>
                    <p>End Point: ${pathData.end_point}</p>
                    <p>Starting Date: ${pathData.starting_date}</p>
                    <p>Estimated Arrival: ${pathData.est_arrival}</p>
                </div>
            `;
        });
    });

    // Delete path
    const deleteButtons = document.querySelectorAll('.delete-path');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this path?')) {
                const id_ruta = this.dataset.route;
                window.location.href = "../../app/Controllers/deletePath.php?id_ruta=1;"
            }
        });
    });

    // Initialize
    collectTableData();
});
</script>