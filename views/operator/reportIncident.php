<?php
include '../../config/connection.php';

// Remove direct database interaction here - we'll move it to a controller

// Add session username check similar to client version
$operator_num = isset($_SESSION['num']) ? $_SESSION['num'] : 'Undefined User';
?>

<div class="container mt-5">
    <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
        <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="h3 mb-0">Report Incident</h2>
                </div>
                <div class="card-body">
                    <form action="../../app/Controllers/incidentController.php" method="POST">
                        <div class="mb-3">
                            <label for="employee_num" class="form-label">Employee Number:</label>
                            <input type="text" class="form-control" id="employee_num" name="employee_num" 
                                value="<?php echo htmlspecialchars($operator_num); ?>" 
                                readonly required style="cursor: not-allowed; pointer-events: none;">
                        </div>
                        
                        <div class="mb-3">
                            <label for="incident_type" class="form-label">Incident Type</label>
                            <select class="form-select" id="incident_type" name="incident_type" required>
                                <option value="">Select incident type</option>
                                <option value="Vehicle">Vehicle Issue</option>
                                <option value="Route">Route Issue</option>
                                <option value="Package">Package Issue</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Incident Date:</label>
                            <input type="date" class="form-control custom-date" id="date" name="date" required max="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Report</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 