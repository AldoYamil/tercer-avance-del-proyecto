<?php
include '../../config/connection.php';

// Obtener el almacén del supervisor desde la sesión
$supervisor_id = $_SESSION['num'] ?? null;
$warehouse_code = null;

if ($supervisor_id) {
    $query = "SELECT warehouse FROM Employees WHERE num = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $supervisor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $warehouse_code = $row['warehouse'];
    }
}

// Obtener el nombre del almacén
$warehouseName = '';
if ($warehouse_code) {
    $query = "SELECT name FROM Warehouse WHERE code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $warehouse_code);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $warehouseName = $row['name'];
    }
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
        <h1 class="h3">Inventory Management - <?php echo htmlspecialchars($warehouseName); ?></h1>
        <div class="d-flex gap-2">
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control" placeholder="Search items...">
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="bi bi-plus-lg"></i> New Item
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name <i class="bi bi-arrow-down-up sort-icon" data-column="name"></i></th>
                            <th>Description</th>
                            <th>Amount <i class="bi bi-arrow-down-up sort-icon" data-column="amount"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT s.*, s.description as item_description 
                        FROM Stock s 
                        LEFT JOIN Inventory inv ON s.code = inv.stock
                        LEFT JOIN Item i ON inv.item = i.code
                        WHERE s.warehouse = ?
                        ORDER BY s.name";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $warehouse_code);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['code']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary edit-item" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editItemModal"
                                            data-item='<?php echo json_encode($row); ?>'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-item" 
                                            data-code="<?php echo $row['code']; ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="5" class="text-center">No items found in this warehouse</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../app/Controllers/newItem.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="warehouse_code" value="<?php echo $warehouse_code; ?>">
                        <div class="mb-3">
                            <label for="code" class="form-label">Item Code</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../app/Controllers/updateItem.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_code" name="code">
                        <input type="hidden" name="warehouse_code" value="<?php echo $warehouse_code; ?>">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="edit_amount" name="amount" required min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Item</button>
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
                    name: cells[1]?.textContent,
                    amount: parseFloat(cells[3]?.textContent) || 0,
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

        // Edit item
        const editButtons = document.querySelectorAll('.edit-item');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemData = JSON.parse(this.dataset.item);
                document.getElementById('edit_code').value = itemData.code;
                document.getElementById('edit_name').value = itemData.name;
                document.getElementById('edit_description').value = itemData.description || '';
                document.getElementById('edit_amount').value = itemData.amount;
            });
        });

        // Delete item
        const deleteButtons = document.querySelectorAll('.delete-item');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this item?')) {
                    const code = this.dataset.code;
                    window.location.href = `../../app/Controllers/deleteItem.php?code=${code}&warehouse=${<?php echo json_encode($warehouse_code); ?>}`;
                }
            });
        });

        // Inicializar datos
        collectTableData();
    });
    </script>
</div>
