<?php
include '../../config/connection.php';

// Fetch vehicles with warehouse info
$sql = "SELECT 
    v.license_plate, 
    v.model, 
    v.max_capacity,
    v.status,
    v.warehouse as warehouse_code,
    w.name AS warehouse_name
FROM Vehicle v
LEFT JOIN Warehouse w ON v.warehouse = w.code
ORDER BY v.license_plate";

$result = $conn->query($sql);

// Fetch warehouses for the forms
$warehouseQuery = "SELECT code, name FROM Warehouse ORDER BY name";
$warehouseResult = $conn->query($warehouseQuery);
$warehouses = [];
while ($row = $warehouseResult->fetch_assoc()) {
    $warehouses[] = $row;
}

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
        <h1 class="h3">Manage Vehicles</h1>
        <div class="d-flex gap-2">
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control" placeholder="Search...">
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                <i class="bi bi-plus-lg"></i> New Vehicle
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>License Plate</th>
                            <th>Model <i class="bi bi-arrow-down-up sort-icon" data-column="model"></i></th>
                            <th>Capacity <i class="bi bi-arrow-down-up sort-icon" data-column="capacity"></i></th>
                            <th>Warehouse <i class="bi bi-arrow-down-up sort-icon" data-column="warehouse"></i></th>
                            <th>Status <i class="bi bi-arrow-down-up sort-icon" data-column="status"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['license_plate']); ?></td>
                                    <td><?php echo htmlspecialchars($row['model']); ?></td>
                                    <td><?php echo htmlspecialchars($row['max_capacity']); ?> kg</td>
                                    <td><?php echo htmlspecialchars($row['warehouse_name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $row['status'] === 'available' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $row['status'] ? htmlspecialchars($row['status']) : 'unavailable'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary edit-vehicle" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editVehicleModal"
                                                data-vehicle='<?php echo json_encode($row); ?>'>
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-vehicle" 
                                                data-license="<?php echo $row['license_plate']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No vehicles found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Vehicle Modal -->
    <div class="modal fade" id="addVehicleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Vehicle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../app/Controllers/newVehicle.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="license_plate" class="form-label">License Plate</label>
                            <input type="text" class="form-control" id="license_plate" name="license_plate" required>
                        </div>
                        <div class="mb-3">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>
                        <div class="mb-3">
                            <label for="max_capacity" class="form-label">Max Capacity (kg)</label>
                            <input type="number" class="form-control" id="max_capacity" name="max_capacity" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="warehouse" class="form-label">Warehouse</label>
                            <select class="form-select" id="warehouse" name="warehouse" required>
                                <?php foreach ($warehouses as $warehouse): ?>
                                    <option value="<?php echo $warehouse['code']; ?>">
                                        <?php echo htmlspecialchars($warehouse['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Vehicle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Vehicle Modal -->
    <div class="modal fade" id="editVehicleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Vehicle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../app/Controllers/updateVehicle.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_license_plate" name="license_plate">
                        <div class="mb-3">
                            <label for="edit_model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="edit_model" name="model" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_max_capacity" class="form-label">Max Capacity (kg)</label>
                            <input type="number" class="form-control" id="edit_max_capacity" name="max_capacity" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_warehouse" class="form-label">Warehouse</label>
                            <select class="form-select" id="edit_warehouse" name="warehouse" required>
                                <?php foreach ($warehouses as $warehouse): ?>
                                    <option value="<?php echo $warehouse['code']; ?>">
                                        <?php echo htmlspecialchars($warehouse['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Vehicle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.querySelector('table tbody');
        const sortIcons = document.querySelectorAll('.sort-icon');
        let tableData = [];
        let sortDirection = {};

        // Recolectar datos iniciales
        function collectTableData() {
            tableData = Array.from(tableBody.querySelectorAll('tr')).map(row => {
                const cells = row.querySelectorAll('td');
                return {
                    element: row,
                    model: cells[1]?.textContent,
                    capacity: parseFloat(cells[2]?.textContent),
                    warehouse: cells[3]?.textContent,
                    status: cells[4]?.textContent.trim(),
                    searchText: Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ')
                };
            });
        }

        // Función de búsqueda
        function filterTable(searchTerm) {
            searchTerm = searchTerm.toLowerCase();
            tableData.forEach(row => {
                const show = row.searchText.includes(searchTerm);
                row.element.style.display = show ? '' : 'none';
            });
        }

        // Función de ordenamiento
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

        // Inicializar datos
        collectTableData();

        // Edit vehicle
        const editButtons = document.querySelectorAll('.edit-vehicle');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const vehicleData = JSON.parse(this.dataset.vehicle);
                document.getElementById('edit_license_plate').value = vehicleData.license_plate;
                document.getElementById('edit_model').value = vehicleData.model;
                document.getElementById('edit_max_capacity').value = vehicleData.max_capacity;
                document.getElementById('edit_warehouse').value = vehicleData.warehouse_code;
                document.getElementById('edit_status').value = vehicleData.status || 'unavailable';
            });
        });

        // Delete vehicle
        const deleteButtons = document.querySelectorAll('.delete-vehicle');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this vehicle?')) {
                    const license = this.dataset.license;
                    window.location.href = `../../app/Controllers/deleteVehicle.php?license_plate=${license}`;
                }
            });
        });
    });
    </script>
</div>
