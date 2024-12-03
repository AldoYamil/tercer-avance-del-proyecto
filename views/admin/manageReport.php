<?php
include '../../config/connection.php';

// Fetch incidents with user info
$sql = "SELECT 
    i.num,
    i.title,
    i.description,
    i.date,
    i.status,
    CONCAT(e.name, ' ', e.lastname) as user_name
FROM Incident i
LEFT JOIN Employees e ON i.user = e.num
ORDER BY i.date DESC";

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
        <h1 class="h3">Manage Reports</h1>
        <div class="d-flex gap-2">
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control" placeholder="Search...">
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReportModal">
                <i class="bi bi-plus-lg"></i> New Report
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title <i class="bi bi-arrow-down-up sort-icon" data-column="title"></i></th>
                            <th>Description</th>
                            <th>Date <i class="bi bi-arrow-down-up sort-icon" data-column="date"></i></th>
                            <th>Status <i class="bi bi-arrow-down-up sort-icon" data-column="status"></i></th>
                            <th>Reported By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['num']); ?></td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($row['date'])); ?></td>
                                    <td>
                                        <span class="badge <?php echo $row['status'] === 'open' ? 'bg-warning' : 'bg-success'; ?>">
                                            <?php echo $row['status'] === 'close' ? 'Closed' : ucfirst(htmlspecialchars($row['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <?php if ($row['status'] === 'open'): ?>
                                                <button class="btn btn-sm btn-outline-success close-report w-100" 
                                                        data-num="<?php echo $row['num']; ?>">
                                                    <i class="bi bi-check-circle"></i> Close
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-warning reopen-report w-100" 
                                                        data-num="<?php echo $row['num']; ?>">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Reopen
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-outline-danger delete-report" 
                                                    data-num="<?php echo $row['num']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No reports found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Report Modal -->
    <div class="modal fade" id="addReportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../app/Controllers/newReport.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Report</button>
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

        function collectTableData() {
            tableData = Array.from(tableBody.querySelectorAll('tr')).map(row => {
                const cells = row.querySelectorAll('td');
                return {
                    element: row,
                    title: cells[1]?.textContent,
                    date: cells[3]?.textContent,
                    status: cells[4]?.textContent.trim(),
                    searchText: Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ')
                };
            });
        }

        function filterTable(searchTerm) {
            searchTerm = searchTerm.toLowerCase();
            tableData.forEach(row => {
                const show = row.searchText.includes(searchTerm);
                row.element.style.display = show ? '' : 'none';
            });
        }

        function sortTable(column) {
            sortDirection[column] = sortDirection[column] === 'asc' ? 'desc' : 'asc';
            
            tableData.sort((a, b) => {
                let comparison = 0;
                if (column === 'date') {
                    const dateA = new Date(a[column]);
                    const dateB = new Date(b[column]);
                    comparison = dateA - dateB;
                } else {
                    if (a[column] < b[column]) comparison = -1;
                    if (a[column] > b[column]) comparison = 1;
                }
                return sortDirection[column] === 'asc' ? comparison : -comparison;
            });

            tableData.forEach(row => {
                tableBody.appendChild(row.element);
            });
        }

        searchInput.addEventListener('input', (e) => {
            filterTable(e.target.value);
        });

        sortIcons.forEach(icon => {
            icon.addEventListener('click', () => {
                const column = icon.dataset.column;
                sortTable(column);
            });
        });

        collectTableData();

        // Agregar manejadores para cerrar/reabrir reportes
        const closeButtons = document.querySelectorAll('.close-report');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to close this report?')) {
                    const num = this.dataset.num;
                    window.location.href = `../../app/Controllers/updateReport.php?num=${num}&status=close`;
                }
            });
        });

        const reopenButtons = document.querySelectorAll('.reopen-report');
        reopenButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to reopen this report?')) {
                    const num = this.dataset.num;
                    window.location.href = `../../app/Controllers/updateReport.php?num=${num}&status=open`;
                }
            });
        });

        // Agregar manejador para eliminar reportes
        const deleteButtons = document.querySelectorAll('.delete-report');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
                    const num = this.dataset.num;
                    window.location.href = `../../app/Controllers/deleteReport.php?num=${num}`;
                }
            });
        });
    });
    </script>
</div>
