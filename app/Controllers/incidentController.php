<?php
include_once '../../config/connection.php';
session_start();

// Determine the user type and redirect path
$userType = isset($_SESSION['role']) ? $_SESSION['role'] : 'client';
$redirectPath = '';
switch($userType) {
    case 'R002': // Operator
        $redirectPath = '../../views/operator/operatorDashboard.php?page=reportIncident';
        break;
    case 'R003': // Transportist
        $redirectPath = '../../views/transportist/transportistDashboard.php?page=reportIncident';
        break;
    default: // Client
        $redirectPath = '../../views/client/clientDashboard.php?page=reportIncident';
}

// Get common fields
$description = $_POST['description'];
$date = $_POST['date'];
$currentDateTime = date('Y-m-d H:i:s');

try {
    // For clients, use client_id instead of num
    $user_id = ($userType === 'client') ? $_SESSION['client_id'] : $_SESSION['num'];
    
    if (!$user_id) {
        throw new Exception("User not authenticated");
    }

    // For employees (operator/transportist), use incident_type as title
    $title = ($userType !== 'client') ? $_POST['incident_type'] : 'Client Incident';

    // Set initial status
    $status = 'Pending';

    $sql_incident = "INSERT INTO Incident (title, description, status, date_incident, date_report, user) 
                     VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_incident = $conn->prepare($sql_incident);
    $stmt_incident->bind_param("sssssi", $title, $description, $status, $date, $currentDateTime, $user_id);
    
    // Execute the prepared statement
    if ($stmt_incident->execute()) {
        header("Location: " . $redirectPath . "&status=success&message=Incident reported successfully");
        exit;
    } else {
        throw new Exception("Error executing query");
    }
    
} catch (Exception $e) {
    header("Location: " . $redirectPath . "&status=error&message=Error reporting incident: " . $e->getMessage());
    exit;
}
?>