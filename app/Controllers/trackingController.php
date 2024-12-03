<?php
include_once '../../config/connection.php';

if (isset($_GET['tracking_code'])) {
    $tracking_code = $_GET['tracking_code'];

    $sql = "SELECT Record.date, Record.location, Record.status, Insurance.insurance_type
            FROM Shipment 
            JOIN Record ON Shipment.num = Record.shipment 
            JOIN Insurance ON Shipment.insurance = Insurance.num
            WHERE Shipment.num = ? 
            ORDER BY Record.date DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo '<div class="alert alert-danger">Lo sentimos, ha ocurrido un error. Por favor intente más tarde.</div>';
        return;
    }

    $stmt->bind_param("s", $tracking_code);
    if (!$stmt->execute()) {
        echo '<div class="alert alert-danger">No se pudo procesar su solicitud. Por favor intente más tarde.</div>';
        return;
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo '<div class="alert alert-warning">No se encontró información para el código de rastreo proporcionado.</div>';
        return;
    }

    $records = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
    }
    $stmt->close();
} 
?>
<title>Tracking</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">

<?php if (!empty($records)): ?>
    <div class="container padding-bottom-3x mb-1">
        <div class="card mb-3">
            <div class="p-4 text-center text-white text-lg rounded-top" style="background: var(--bs-primary);">
                <span class="text-uppercase">Tracking Order No - </span>
                <span class="text-medium text-white"><?php echo htmlspecialchars($tracking_code); ?></span>
            </div>
            
            <!-- Optional: Shipping Details Section -->
            <div class="d-flex flex-wrap flex-sm-nowrap justify-content-between py-3 px-2 bg-body">
                <div class="w-100 text-center py-1 px-2">
                    <span class="text-medium">Insurance:</span> <?php echo htmlspecialchars($records[0]['insurance_type']); ?>
                </div>
                <div class="w-100 text-center py-1 px-2">
                    <span class="text-medium">Status:</span> <?php echo $records[0]['status']; ?>
                </div>
                <div class="w-100 text-center py-1 px-2">
                    <span class="text-medium">Last Update:</span> <?php echo date('M d, Y', strtotime($records[0]['date'])); ?>
                </div>
            </div>

            <div class="card-body">
                <div class="steps d-flex flex-wrap flex-sm-nowrap justify-content-between padding-top-2x padding-bottom-1x">
                    <?php
                    $statuses = [
                        ['name' => 'Order Placed', 'display' => 'Order Received', 'icon' => 'pe-7s-note'],
                        ['name' => 'In Process', 'display' => 'In Process', 'icon' => 'pe-7s-box2'],
                        ['name' => 'In Transit', 'display' => 'In Transit', 'icon' => 'pe-7s-car'],
                        ['name' => 'Delivered', 'display' => 'Delivered', 'icon' => 'pe-7s-check']
                    ];
                    
                    $currentStatus = end($records)['status'];
                    foreach ($statuses as $index => $status):
                        $isCompleted = false;
                        foreach ($records as $record) {
                            if ($record['status'] === $status['name']) {
                                $isCompleted = true;
                                break;
                            }
                        }
                    ?>
                        <div class="step <?php echo $isCompleted ? 'completed' : ''; ?>">
                            <div class="step-icon-wrap">
                                <div class="step-icon"><i class="<?php echo $status['icon']; ?>"></i></div>
                            </div>
                            <h4 class="step-title"><?php echo $status['display']; ?></h4>
                            <?php if ($isCompleted): ?>
                                <?php foreach ($records as $record): ?>
                                    <?php if ($record['status'] === $status['name']): ?>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y', strtotime($record['date'])); ?><br>
                                            <?php echo $record['location']; ?>
                                        </small>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <style>

        .card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .steps .step {
            display: block;
            width: 100%;
            margin-bottom: 35px;
            text-align: center;
        }
        .steps .step .step-icon-wrap {
            display: block;
            position: relative;
            width: 100%;
            height: 80px;
            text-align: center;
        }
        .steps .step .step-icon-wrap::before,
        .steps .step .step-icon-wrap::after {
            display: block;
            position: absolute;
            top: 50%;
            width: 50%;
            height: 3px;
            margin-top: -1px;
            background-color: #e1e7ec;
            content: '';
            z-index: 1;
        }
        .steps .step .step-icon-wrap::before {
            left: 0;
        }
        .steps .step .step-icon-wrap::after {
            right: 0;
        }
        .steps .step .step-icon {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
            border: 1px solid #e1e7ec;
            border-radius: 50%;
            background-color: #f5f5f5;
            color: #374250;
            font-size: 38px;
            line-height: 81px;
            z-index: 5;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .steps .step .step-title {
            margin-top: 16px;
            margin-bottom: 0;
            color: #606975;
            font-size: 14px;
            font-weight: 500;
        }
        .steps .step:first-child .step-icon-wrap::before {
            display: none;
        }
        .steps .step:last-child .step-icon-wrap::after {
            display: none;
        }
        .steps .step.completed .step-icon-wrap::before,
        .steps .step.completed .step-icon-wrap::after {
            background-color: #0da9ef;
        }
        .steps .step.completed .step-icon {
            border-color: #0da9ef;
            background: linear-gradient(135deg, #0da9ef 0%, #0077cc 100%);
            color: #fff;
        }
        .text-medium {
            font-weight: 600;
            color: #2c3e50;
        }
        @media (max-width: 576px) {
            .flex-sm-nowrap .step .step-icon-wrap::before,
            .flex-sm-nowrap .step .step-icon-wrap::after {
                display: none;
            }
        }
    </style>
<?php endif; ?>

