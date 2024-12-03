<?php
// Session is already started in dashboard
if (!isset($_SESSION['client_id'])) {
    header("Location: ../auth/LoginViewUser.php");
    exit();
}

$client_username = $_SESSION['client_username'];
$client_num = $_SESSION['client_id']; // Changed from 'num' to 'client_id'
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Incident</title>
</head>
<body class="bg-body">
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
                        <h1 class="h3 mb-0">Report Incident</h1>
                    </div>
                    <div class="card-body">
                        <form action="../../app/Controllers/incidentController.php" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username:</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                    value="<?php echo htmlspecialchars($client_username); ?>" 
                                    readonly required style="cursor: not-allowed; pointer-events: none;">
                                <!-- Changed to use client_id -->
                                <input type="hidden" name="num" value="<?php echo htmlspecialchars($client_num); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Incident Description:</label>
                                <textarea class="form-control" id="description" name="description" 
                                    rows="4" required></textarea>
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
</body>
</html>