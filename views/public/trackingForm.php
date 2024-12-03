<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track a package</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<?php include_once '../partials/header.php'; ?>
<body class="bg-body">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-shipping-fast fa-3x text-primary mb-3" style="color: var(--blue);"></i>
                            <h2 class="card-title">Track Your Package</h2>
                            <p class="text-muted">Enter your tracking number to get real-time updates</p>
                        </div>
                        
                        <form id="trackingForm" method="GET" action="" class="tracking-form">
                            <div class="input-group mb-3">
                                <span class="input-group-text border-end-0">
                                    <i class="fas fa-search text-primary"></i>
                                </span>
                                <input type="text" 
                                       class="form-control border-start-0 ps-0" 
                                       id="tracking_code" 
                                       name="tracking_code" 
                                       placeholder="Enter tracking number..."
                                       required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3 text-uppercase fw-bold">
                                Track Package
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="tracking-results" class="mt-4">
            <?php 
            if (isset($_GET['tracking_code'])) {
                include_once '../../app/Controllers/trackingController.php';
            }
            ?>
        </div>
    </div>

    <style>
    body {
        background: #0d6efd;
        min-height: 100vh;
    }
    
    .card {
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .input-group-text {
        background: var(--bs-body-bg);
        border-color: var(--bs-border-color);
    }
    
    .form-control {
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
        border-color: var(--bs-border-color);
    }
    
    .form-control:focus {
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
        box-shadow: none;
        border-color: var(--bs-border-color);
    }
    
    .text-muted {
        color: var(--bs-secondary-color) !important;
    }
    
    .btn-primary {
        border-radius: 10px;
    }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>