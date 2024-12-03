<?php
include '../../config/connection.php';

// Fetch warehouses
$sql = "SELECT * FROM Warehouse ORDER BY code";
$result = $conn->query($sql);

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
        <h1 class="h3">Manage Warehouses</h1>
        <div class="d-flex gap-2">
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control" placeholder="Search...">
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWarehouseModal">
                <i class="bi bi-plus-lg"></i> New Warehouse
            </button>
        </div>
    </div>

    <!-- Warehouses Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name <i class="bi bi-arrow-down-up sort-icon" data-column="name"></i></th>
                            <th>Address <i class="bi bi-arrow-down-up sort-icon" data-column="address"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['street'] . ' ' . $row['number'] . ', ' . $row['colony']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary edit-warehouse" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editWarehouseModal"
                                                data-warehouse='<?php echo json_encode($row); ?>'>
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-warehouse" 
                                                data-code="<?php echo $row['code']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No warehouses found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Warehouse Modal -->
    <div class="modal fade" id="addWarehouseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../app/Controllers/newWarehouse.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="code" class="form-label">Warehouse Code</label>
                            <input type="text" class="form-control" id="code" name="code" maxlength="6" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="street" class="form-label">Street</label>
                            <input type="text" class="form-control" id="street" name="street" required>
                        </div>
                        <div class="mb-3">
                            <label for="colony" class="form-label">Colony</label>
                            <input type="text" class="form-control" id="colony" name="colony" required>
                        </div>
                        <div class="mb-3">
                            <label for="number" class="form-label">Number</label>
                            <input type="text" class="form-control" id="number" name="number" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Warehouse</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Warehouse Modal -->
    <div class="modal fade" id="editWarehouseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Warehouse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../app/Controllers/updateWarehouse.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_code" name="code">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_street" class="form-label">Street</label>
                            <input type="text" class="form-control" id="edit_street" name="street" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_colony" class="form-label">Colony</label>
                            <input type="text" class="form-control" id="edit_colony" name="colony" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_number" class="form-label">Number</label>
                            <input type="text" class="form-control" id="edit_number" name="number" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Warehouse</button>
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

        // Recolectar datos iniciales de la tabla
        function collectTableData() {
            tableData = Array.from(tableBody.querySelectorAll('tr')).map(row => {
                const cells = row.querySelectorAll('td');
                return {
                    element: row,
                    name: cells[1]?.textContent,
                    address: cells[2]?.textContent,
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

        // Edit warehouse
        const editButtons = document.querySelectorAll('.edit-warehouse');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const warehouseData = JSON.parse(this.dataset.warehouse);
                document.getElementById('edit_code').value = warehouseData.code;
                document.getElementById('edit_name').value = warehouseData.name;
                document.getElementById('edit_street').value = warehouseData.street;
                document.getElementById('edit_colony').value = warehouseData.colony;
                document.getElementById('edit_number').value = warehouseData.number;
            });
        });

        // Delete warehouse
        const deleteButtons = document.querySelectorAll('.delete-warehouse');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this warehouse?')) {
                    const code = this.dataset.code;
                    window.location.href = `../../app/Controllers/deleteWarehouse.php?code=${code}`;
                }
            });
        });
    });
    </script>
</div>

