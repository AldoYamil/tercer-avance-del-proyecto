<?php
include '../../config/connection.php';

// Get operator's warehouse from session
$operator_id = $_SESSION['num'] ?? null;
$warehouse_code = null;

if ($operator_id) {
    $query = "SELECT warehouse FROM Employees WHERE num = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $operator_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $warehouse_code = $row['warehouse'];
    }
}

// Get warehouse name
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

$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>

<div class="container-fluid py-4">
    <?php if ($message): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Stock Management - <?php echo htmlspecialchars($warehouseName); ?></h1>
        <div class="search-box">
            <input type="text" id="searchInput" class="form-control" placeholder="Search items...">
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
                            <th>Current Stock <i class="bi bi-arrow-down-up sort-icon" data-column="amount"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT s.*, i.description as item_description 
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
                                <td>
                                    <span class="fw-bold"><?php echo htmlspecialchars($row['amount']); ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary adjust-stock" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#adjustStockModal"
                                            data-item='<?php echo json_encode($row); ?>'>
                                        <i class="bi bi-box-seam"></i> Adjust Stock
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary view-history"
                                            data-bs-toggle="modal"
                                            data-bs-target="#historyModal"
                                            data-code="<?php echo $row['code']; ?>">
                                        <i class="bi bi-clock-history"></i>
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

    <!-- Adjust Stock Modal -->
    <div class="modal fade" id="adjustStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Stock Level</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../app/Controllers/adjustStock.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="adjust_code" name="code">
                        <input type="hidden" name="warehouse_code" value="<?php echo $warehouse_code; ?>">
                        
                        <div class="item-details mb-4">
                            <h6 class="item-name mb-2"></h6>
                            <p class="current-stock mb-2"></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adjustment Type</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="adjustment_type" id="add" value="add" checked>
                                <label class="btn btn-outline-success" for="add">Add Stock</label>
                                
                                <input type="radio" class="btn-check" name="adjustment_type" id="remove" value="remove">
                                <label class="btn btn-outline-danger" for="remove">Remove Stock</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adjustment_amount" class="form-label">Amount to Adjust</label>
                            <input type="number" class="form-control" id="adjustment_amount" 
                                   name="adjustment_amount" required min="1">
                        </div>

                        <div class="mb-3">
                            <label for="adjustment_reason" class="form-label">Reason for Adjustment</label>
                            <textarea class="form-control" id="adjustment_reason" 
                                      name="adjustment_reason" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm Adjustment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Stock History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stock Movement History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="item-name-header mb-3"></h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Reason</th>
                                    <th>Operator</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <!-- Filled dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize table data and sort direction
        const tableBody = document.querySelector('tbody');
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        const sortDirection = { name: 'asc', amount: 'asc' };
        
        // Create searchable data structure
        const tableData = rows.map(row => ({
            element: row,
            name: row.children[1]?.textContent.toLowerCase() || '',
            amount: parseInt(row.children[3]?.textContent) || 0,
            searchText: Array.from(row.children)
                .map(cell => cell.textContent.toLowerCase())
                .join(' ')
        }));

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', (e) => {
            filterTable(e.target.value);
        });

        function filterTable(searchTerm) {
            searchTerm = searchTerm.toLowerCase();
            tableData.forEach(row => {
                const show = row.searchText.includes(searchTerm);
                row.element.style.display = show ? '' : 'none';
            });
        }

        // Sorting functionality
        document.querySelectorAll('.sort-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const column = this.dataset.column;
                sortTable(column);
                
                // Update sort icons
                document.querySelectorAll('.sort-icon').forEach(icon => {
                    icon.classList.remove('bi-arrow-up', 'bi-arrow-down');
                });
                
                this.classList.add(
                    sortDirection[column] === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'
                );
            });
        });

        function sortTable(column) {
            sortDirection[column] = sortDirection[column] === 'asc' ? 'desc' : 'asc';
            
            tableData.sort((a, b) => {
                let comparison = 0;
                if (column === 'amount') {
                    comparison = a[column] - b[column];
                } else {
                    comparison = a[column].localeCompare(b[column]);
                }
                return sortDirection[column] === 'asc' ? comparison : -comparison;
            });

            // Clear and repopulate table
            tableData.forEach(row => {
                tableBody.appendChild(row.element);
            });
        }

        // Adjust stock modal handler
        document.querySelectorAll('.adjust-stock').forEach(button => {
            button.addEventListener('click', function() {
                const itemData = JSON.parse(this.dataset.item);
                document.getElementById('adjust_code').value = itemData.code;
                document.querySelector('.item-name').textContent = itemData.name;
                document.querySelector('.current-stock').textContent = 
                    `Current Stock Level: ${itemData.amount}`;
            });
        });

        // View history handler with improved error handling
        document.querySelectorAll('.view-history').forEach(button => {
            button.addEventListener('click', async function() {
                const code = this.dataset.code;
                const tbody = document.getElementById('historyTableBody');
                const itemNameHeader = document.querySelector('.item-name-header');
                
                // Show loading state
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            Loading history...
                        </td>
                    </tr>
                `;

                try {
                    const response = await fetch(`../../app/Controllers/getStockHistory.php?code=${code}`);
                    const jsonData = await response.json();

                    if (!response.ok) {
                        throw new Error(jsonData.error || 'Failed to load history');
                    }

                    if (!jsonData.success) {
                        throw new Error(jsonData.error);
                    }

                    const data = jsonData.data;

                    if (data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center">No movement history found</td>
                            </tr>
                        `;
                        return;
                    }

                    // Set the item name in the header
                    itemNameHeader.textContent = `History for: ${data[0].item_name}`;

                    // Render the movements
                    tbody.innerHTML = data.map(movement => `
                        <tr>
                            <td>${movement.date}</td>
                            <td>
                                <span class="badge ${movement.type === 'Added' ? 'bg-success' : 'bg-danger'}">
                                    ${movement.type}
                                </span>
                            </td>
                            <td>${movement.amount}</td>
                            <td>${movement.reason}</td>
                            <td>${movement.operator}</td>
                        </tr>
                    `).join('');

                } catch (error) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                ${error.message}
                            </td>
                        </tr>
                    `;
                }
            });
        });
    });
    </script>
</div>
