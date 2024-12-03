<?php
session_start();
include '../../config/connection.php';

// Debug information
error_log("POST data received: " . print_r($_POST, true));
error_log("FILES data received: " . print_r($_FILES, true));

if (!isset($_SESSION['client_id']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if it's a preset image first
if (isset($_POST['preset_image'])) {
    $preset_image = $_POST['preset_image'];
    $preset_path = '../../data/pfp/presets/' . $preset_image;
    
    if (!file_exists($preset_path)) {
        echo json_encode(['success' => false, 'message' => 'Preset image not found']);
        exit;
    }

    // Create upload directory if it doesn't exist
    $upload_dir = '../../data/pfp/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate new filename for the preset image
    $extension = pathinfo($preset_image, PATHINFO_EXTENSION);
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;

    // Copy preset image
    if (copy($preset_path, $filepath)) {
        // Delete old profile picture if it exists and isn't default
        $stmt = $conn->prepare("SELECT profile_picture FROM Users WHERE num = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $row = $result->fetch_assoc()) {
            $old_picture = $row['profile_picture'];
            if ($old_picture && $old_picture !== 'default.jpg') {
                $old_filepath = $upload_dir . $old_picture;
                if (file_exists($old_filepath)) {
                    unlink($old_filepath);
                }
            }
        }

        // Update database
        $stmt = $conn->prepare("UPDATE Users SET profile_picture = ? WHERE num = ?");
        $stmt->bind_param("si", $filename, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['profile_picture'] = $filename;
            echo json_encode([
                'success' => true, 
                'filename' => $filename,
                'message' => 'Profile picture updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Database update failed'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to copy preset image'
        ]);
    }
    exit;
}

// If not a preset image, check for uploaded file
if (!isset($_FILES['profile_picture'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['profile_picture'];

// Validate file
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

// Validate file size (5MB max)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
    exit;
}

// Create upload directory if it doesn't exist
$upload_dir = '../../data/pfp/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
$filepath = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // Delete old profile picture if it exists and isn't default
    $stmt = $conn->prepare("SELECT profile_picture FROM Users WHERE num = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $row = $result->fetch_assoc()) {
        $old_picture = $row['profile_picture'];
        if ($old_picture && $old_picture !== 'default.jpg') {
            $old_filepath = $upload_dir . $old_picture;
            if (file_exists($old_filepath)) {
                unlink($old_filepath);
            }
        }
    }

    // Update database
    $stmt = $conn->prepare("UPDATE Users SET profile_picture = ? WHERE num = ?");
    $stmt->bind_param("si", $filename, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['profile_picture'] = $filename;
        
        // Log success for debugging
        error_log("Profile picture updated successfully for user_id: $user_id");
        error_log("New filename: $filename");
        
        echo json_encode([
            'success' => true, 
            'filename' => $filename,
            'message' => 'Profile picture updated successfully'
        ]);
    } else {
        // Log error for debugging
        error_log("Database update failed: " . $conn->error);
        echo json_encode([
            'success' => false, 
            'message' => 'Database update failed: ' . $conn->error
        ]);
    }
} else {
    // Log error for debugging
    error_log("Failed to move uploaded file to: $filepath");
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to upload file'
    ]);
}

// Close database connection
$conn->close();