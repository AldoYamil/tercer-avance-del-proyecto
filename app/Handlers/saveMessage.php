<?php
header('Content-Type: application/json');

try {
    // Validate form data
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        throw new Exception('All fields are required');
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Prepare message data
    $messageData = [
        'id' => uniqid(),
        'timestamp' => date('Y-m-d H:i:s'),
        'name' => htmlspecialchars($_POST['name']),
        'email' => htmlspecialchars($_POST['email']),
        'subject' => htmlspecialchars($_POST['subject']),
        'message' => htmlspecialchars($_POST['message']),
        'status' => 'unread'
    ];

    // Path to JSON file
    $jsonFile = '../../data/messages.json';
    
    // Create directory if it doesn't exist
    if (!file_exists('../../data')) {
        mkdir('../../data', 0777, true);
    }

    $messages = [];
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        $messages = json_decode($jsonContent, true) ?? [];
    }
    $messages[] = $messageData;

    // Save to JSON file
    if (file_put_contents($jsonFile, json_encode($messages, JSON_PRETTY_PRINT))) {
        echo json_encode([
            'success' => true,
            'message' => 'Message saved successfully'
        ]);
    } else {
        throw new Exception('Error saving message');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
