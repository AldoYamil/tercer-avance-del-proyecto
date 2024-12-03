<?php 
session_start(); 
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'R002') {
    header("Location: ../auth/LoginViewEmployee.php");
    exit();
}
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/LoginViewEmployee.php");
    exit();
}

// Add a unique identifier for the current user's session
$userSessionId = $_SESSION['username'] . '_dashboard_visited';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Panel</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/theme-switcher.js"></script>
    <script src="../../assets/js/main.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/dashboardstyle.css">
    <script>
        function isFirstVisit() {
            const sessionId = '<?php echo $userSessionId; ?>';
            if (!localStorage.getItem(sessionId)) {
                localStorage.setItem(sessionId, 'true');
                return true;
            }
            return false;
        }
    </script>
</head>
<body>
    <div class="dashboard-container">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-auto col-md-2 sidebar bg-dark">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <a class="d-flex align-items-center pb-3 mb-md-1 mt-md-3 me-md-auto text-white text-decoration-none">
                        <span class="fs-5 fw-bolder d-none d-sm-inline">Operator Panel</span>
                        <span class="fs-5 d-sm-none">OP</span>
                    </a>

                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100" id="menu">
                        <!-- Dashboard -->
                        <li class="nav-item w-100">
                            <a href="?page=dashboard" data-page="dashboard" class="nav-link px-0 text-white">
                                <i class="fs-4 bi-grid"></i> <span class="ms-1 d-none d-sm-inline">Dashboard</span>
                            </a>
                        </li>

                        <!-- Assigned Package -->
                        <li class="nav-item w-100">
                            <a href="?page=assignedPackage" data-page="assignedPackage" class="nav-link px-0 text-white">
                                <i class="fs-4 bi-box-seam"></i> <span class="ms-1 d-none d-sm-inline">Assigned Package</span>
                            </a>
                        </li>

                        <!-- Manage Stock -->
                        <li class="nav-item w-100">
                            <a href="?page=manageStock" data-page="manageStock" class="nav-link px-0 text-white">
                                <i class="fs-4 bi-boxes"></i> <span class="ms-1 d-none d-sm-inline">Manage Stock</span>
                            </a>
                        </li>

                        <!-- Report Incident -->
                        <li class="nav-item w-100">
                            <a href="?page=reportIncident" data-page="reportIncident" class="nav-link px-0 text-white">
                                <i class="fs-4 bi-exclamation-triangle"></i> <span class="ms-1 d-none d-sm-inline">Report Incident</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Fixed Header -->
                <header class="dashboard-header">
                    <nav class="navbar navbar-expand-lg navbar-light bg-body px-3">
                        <div class="container-fluid">
                            <button class="navbar-toggler d-md-none" type="button" id="sidebarToggle">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <h1 class="navbar-brand mb-0 h1">Dashboard</h1>
                            <div class="ms-auto d-flex align-items-center">
                                <!-- Theme Switcher -->
                                <button class="btn btn-link nav-link px-3 me-2" id="theme-switcher">
                                    <i class="bi bi-sun-fill icon-sun"></i>
                                    <i class="bi bi-moon-fill icon-moon d-none"></i>
                                </button>
                                
                                <!-- User Dropdown -->
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-person-circle fs-4 me-2"></i>
                                        <span class="me-2"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Operator'; ?></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <li><a class="dropdown-item" href="?page=profileInfo"><i class="bi bi-person me-2"></i>Profile</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="../../app/Controllers/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </nav>
                </header>

                <!-- Content Area -->
                <div class="content-wrapper">
                    <div class="d-flex flex-column h-100">
                        <div class="flex-grow-1">
                            <div class="position-relative">
                                <div id="loading-spinner" class="position-absolute top-50 start-50 translate-middle d-none">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                                <div id="content-area">
                                    <?php
                                    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
                                    
                                    $pages = [
                                        'dashboard' => 'dashboardContent.php',
                                        'assignedPackage' => 'assignedPackage.php',
                                        'manageStock' => 'manageStock.php',
                                        'reportIncident' => 'reportIncident.php',
                                        'profileInfo' => 'profileInfo.php'
                                    ];
                                    
                                    if (isset($pages[$page])) {
                                        include $pages[$page];
                                    } else {
                                        include 'dashboardContent.php';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="dashboard-footer">
                    <?php include '../partials/footerPanel.php'; ?>
                </footer>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // ... Same scripts as admin dashboard ...
    </script>
</body>
</html>

