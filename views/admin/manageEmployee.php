<?php
include '../../config/connection.php';

// Fetch employees (excluding supervisors)
$sql = "
    SELECT 
        e.num AS num, 
        e.name, 
        e.lastname, 
        e.surname, 
        w.name AS warehouse_name, 
        e.status,
        e.warehouse as warehouse_code,
        CASE 
            WHEN e.role = 'R002' THEN 'Operator'
            WHEN e.role = 'R003' THEN 'Driver'
            ELSE 'Unknown'
        END as role_name,
        e.role
    FROM Employees e
    JOIN Warehouse w ON e.warehouse = w.code
    WHERE e.role IN ('R002', 'R003')
    ORDER BY e.num;
";
$result = $conn->query($sql);

// Fetch warehouses for the forms
$warehouseQuery = "SELECT code, name FROM Warehouse ORDER BY name";
$warehouseResult = $conn->query($warehouseQuery);
$warehouses = [];
while ($row = $warehouseResult->fetch_assoc()) {
    $warehouses[] = $row;
}
?>

<div class="container-fluid py-4">
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Employees</h1>
        <div class="d-flex gap-2">
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control" placeholder="Search...">
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                <i class="bi bi-plus-lg"></i> New Employee
            </button>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name <i class="bi bi-arrow-down-up sort-icon" data-column="name"></i></th>
                            <th>Role <i class="bi bi-arrow-down-up sort-icon" data-column="role"></i></th>
                            <th>Warehouse <i class="bi bi-arrow-down-up sort-icon" data-column="warehouse"></i></th>
                            <th>Status <i class="bi bi-arrow-down-up sort-icon" data-column="status"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['num']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name'] . ' ' . $row['lastname'] . ' ' . ($row['surname'] ? $row['surname'] : '')); ?></td>
                                    <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['warehouse_name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $row['status'] === 'available' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $row['status'] ? htmlspecialchars($row['status']) : 'unavailable'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary edit-employee" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editEmployeeModal"
                                                data-employee='<?php echo json_encode($row); ?>'>
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-employee" 
                                                data-num="<?php echo $row['num']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No employees found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addEmployeeForm" action="../../app/Controllers/newEmployee.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                        <div class="mb-3">
                            <label for="surname" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="surname" name="surname">
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="R002">Operator</option>
                                <option value="R003">Driver</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_code" class="form-label">Warehouse</label>
                            <select class="form-select" id="warehouse_code" name="warehouse_code" required>
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
                        <button type="submit" class="btn btn-primary">Add Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editEmployeeForm" action="../../app/Controllers/updateEmployee.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_num" name="num">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_lastname" name="lastname" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_surname" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="edit_surname" name="surname">
                        </div>
                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Role</label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="R002">Operator</option>
                                <option value="R003">Driver</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_warehouse_code" class="form-label">Warehouse</label>
                            <select class="form-select" id="edit_warehouse_code" name="warehouse_code" required>
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
                        <button type="submit" class="btn btn-primary">Update Employee</button>
                    </div>
                </form>
            </div>
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
                    role: cells[2]?.textContent,
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

            // Reordenar elementos en el DOM
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

        // Mantener el código existente para edit y delete
        const editButtons = document.querySelectorAll('.edit-employee');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const employeeData = JSON.parse(this.dataset.employee);
                document.getElementById('edit_num').value = employeeData.num;
                document.getElementById('edit_name').value = employeeData.name;
                document.getElementById('edit_lastname').value = employeeData.lastname;
                document.getElementById('edit_surname').value = employeeData.surname;
                document.getElementById('edit_role').value = employeeData.role;
                document.getElementById('edit_warehouse_code').value = employeeData.warehouse_code;
                document.getElementById('edit_status').value = employeeData.status || 'unavailable';
            });
        });

        const deleteButtons = document.querySelectorAll('.delete-employee');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this employee?')) {
                    const num = this.dataset.num;
                    window.location.href = `../../app/Controllers/deleteEmployee.php?num=${num}`;
                }
            });
        });
    });
</script>