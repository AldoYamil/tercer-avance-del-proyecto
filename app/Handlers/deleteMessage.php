<?php
header('Content-Type: application/json');

$messageId = $_POST['messageId'] ?? '';
$messagesFile = __DIR__ . '/../../data/messages.json';

if (empty($messageId)) {
    echo json_encode(['success' => false, 'message' => 'Message ID is required']);
    exit;
}

$messages = json_decode(file_get_contents($messagesFile), true);
$messageIndex = array_search($messageId, array_column($messages, 'id'));

if ($messageIndex !== false) {
    array_splice($messages, $messageIndex, 1);
    file_put_contents($messagesFile, json_encode($messages, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Message not found']);
}
