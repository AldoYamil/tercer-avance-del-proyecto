<?php
include '../../config/connection.php';

$employee_num = $_SESSION['num'];

// Get employee information with role and warehouse names
$sql = "SELECT e.num, e.name, e.lastname, e.surname, 
        e.role, e.warehouse, e.username, e.status,
        r.name AS role_name,
        w.name AS warehouse_name
        FROM Employees e
        LEFT JOIN Role r ON e.role = r.code
        LEFT JOIN Warehouse w ON e.warehouse = w.code
        WHERE e.num = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_num);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$stmt->close();

if (!$employee) {
    $employee = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Profile</title>
    <style>
        .profile-field {
            padding: 1rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
        }
        
        .profile-field:hover {
            background-color: #B0E2FF;
            color: #000;
        }
        
        .edit-btn {
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }
        
        .profile-field:hover .edit-btn {
            opacity: 1;
        }
        
        .loading {
            position: relative;
            opacity: 0.7;
            pointer-events: none;
        }
        
        .loading::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .loading::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 1;
            width: 20px;
            height: 20px;
            border: 3px solid #007bff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">My Profile</h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="row g-4">
                            <!-- Personal Information Section -->
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">Personal Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="profile-field">
                                            <label class="text-muted small">Employee ID</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-num"><?php echo htmlspecialchars($employee['num']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="profile-field">
                                            <label class="text-muted small">Name</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-name"><?php echo htmlspecialchars($employee['name'] . ' ' . $employee['lastname'] . ' ' . $employee['surname']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="profile-field">
                                            <label class="text-muted small">Warehouse</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-warehouse"><?php echo htmlspecialchars($employee['warehouse_name']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Work Information Section -->
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">Work Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="profile-field">
                                            <label class="text-muted small">Role</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-role"><?php echo htmlspecialchars($employee['role_name']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-field">
                                            <label class="text-muted small">Username</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-username"><?php echo htmlspecialchars($employee['username']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="fieldName">
                        <div class="mb-3">
                            <label for="fieldValue" class="form-label" id="fieldLabel"></label>
                            <input type="text" class="form-control" id="fieldValue">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        const toast = new bootstrap.Toast(document.getElementById('toast'));
        
        // Handle individual edit buttons
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const field = this.dataset.field;
                const currentValue = document.getElementById(`display-${field}`).textContent.trim();
                
                document.getElementById('fieldName').value = field;
                document.getElementById('fieldValue').value = currentValue;
                document.getElementById('fieldLabel').textContent = 
                    this.closest('.profile-field').querySelector('label').textContent;
                
                editModal.show();
            });
        });

        // Handle save changes
        document.getElementById('saveChanges').addEventListener('click', async function() {
            const field = document.getElementById('fieldName').value;
            const value = document.getElementById('fieldValue').value;
            const displayElement = document.getElementById(`display-${field}`);
            const profileField = displayElement.closest('.profile-field');
            
            // Validate empty values
            if (!value.trim()) {
                document.querySelector('.toast-body').textContent = 'Please enter a value';
                toast.show();
                return;
            }
            
            // Add loading state
            profileField.classList.add('loading');
            
            try {
                const response = await fetch('../../app/Controllers/updateOperatorProfile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        field: field,
                        value: value.trim()
                    }),
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    displayElement.textContent = value.trim();
                    document.querySelector('.toast-body').textContent = 'Updated successfully!';
                    editModal.hide();
                } else {
                    document.querySelector('.toast-body').textContent = data.message || 'Update failed';
                }
            } catch (error) {
                console.error('Error:', error);
                document.querySelector('.toast-body').textContent = 'An error occurred. Please try again.';
            } finally {
                profileField.classList.remove('loading');
                toast.show();
            }
        });

        // Handle modal form submission
        document.getElementById('editForm').addEventListener('submit', (e) => {
            e.preventDefault();
            document.getElementById('saveChanges').click();
        });
    });
    </script>
</body>
</html> 