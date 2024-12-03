<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics - CAFPATH</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>
    
    <div class="container-fluid p-0">
        <!-- Banner Section -->
        <div class="banner position-relative">
            <img src="../../assets/img/logistics-banner.png" class="w-100" style="height: 400px; object-fit: cover;" alt="Logistics Operations Banner">
            <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
                <h1 class="display-4 fw-bold" data-translate="logisticsBannerTitle">Logistics Operations</h1>
                <p class="lead" data-translate="logisticsBannerDesc">Optimizing supply chain operations through advanced logistics solutions</p>
            </div>
        </div>

        <!-- Main Content Container -->
        <div class="container my-5">
            <!-- Introduction Section -->
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="text-center mb-4" style="color: var(--blue);" data-translate="logisticsSystemTitle">CAFPATH Logistics Network: Excellence in Operations</h2>
                    <p class="lead text-center" data-translate="logisticsIntro">
                        At CAFPATH, our logistics network represents the backbone of our operations...
                    </p>
                </div>
            </div>

            <!-- Warehouse Section -->
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <h3 style="color: var(--darkgrey);" data-translate="warehouseNetworkTitle">Strategic Warehouse Network</h3>
                    <p data-translate="warehouseNetworkDesc">Our warehouse network is strategically positioned to optimize distribution:</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle-fill me-2" style="color: var(--skyblue);"></i> <span data-translate="warehouseList1">Automated storage and retrieval systems (AS/RS)</span></li>
                        <!-- ... otros elementos de la lista ... -->
                    </ul>
                </div>
                <div class="col-lg-6">
                    <img src="../../assets/img/warehouse-operations.jpg" class="img-fluid rounded hover-scale" alt="Warehouse operations">
                </div>
            </div>

            <!-- Fleet Management Cards -->
            <div class="row mb-5">
                <h3 class="text-center mb-4" style="color: var(--blue);" data-translate="fleetManagementTitle">Advanced Fleet Management</h3>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 hover-scale">
                        <div class="card-body">
                            <h5 class="card-title" style="color: var(--darkgrey);"><i class="fas fa-truck-loading"></i> <span data-translate="fleetType1Title">Heavy-Duty Fleet</span></h5>
                            <ul class="list-unstyled">
                                <li data-translate="fleetType1List1">Long-haul trucks for interstate transport</li>
                                <!-- ... otros elementos de la lista ... -->
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- ... segunda tarjeta de fleet management ... -->
            </div>

            <!-- ... continuar con las demás secciones siguiendo el mismo patrón ... -->

        </div>
    </div>

    <?php include '../partials/footerViews.php'; ?>
</body>
</html>