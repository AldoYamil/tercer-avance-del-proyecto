<?php
include '../../config/connection.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: ../auth/LoginViewUser.php");
    exit;
}

$client_id = $_SESSION['client_id'];

// Get client information
$sql_client = "SELECT c.num, c.name, c.lastname, c.surname, c.company, c.phone, c.street, c.colony, c.number, 
                      u.username, u.profile_picture 
               FROM Client AS c
               INNER JOIN Users AS u ON c.usernum = u.num
               WHERE c.num = ?";

$stmt_client = $conn->prepare($sql_client);
$stmt_client->bind_param("i", $client_id);
$stmt_client->execute();
$result_client = $stmt_client->get_result();

// Debug information
if ($stmt_client->error) {
    die("Error en la consulta: " . $stmt_client->error);
}

$client = $result_client->fetch_assoc();

// Debug information
if (!$client) {
    error_log("No se encontraron datos para client_id: " . $client_id);
    $client = [
        'name' => '',
        'lastname' => '',
        'surname' => '',
        'company' => '',
        'phone' => '',
        'street' => '',
        'colony' => '',
        'number' => '',
        'username' => '',
        'profile_picture' => 'default.jpg'
    ];
}

// Debug output
error_log("Client ID: " . $client_id);
error_log("SQL Query: " . $sql_client);
error_log("Client data: " . print_r($client, true));

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Obtener los datos del cliente desde la sesión
$client_data = $_SESSION['client_data'];
$profile_picture = $_SESSION['profile_picture'] ?? 'default.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        .profile-field {
            padding: 1rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
        }
        
        .profile-field:hover {
            background-color: #B0E2FF;
            color: #000;
        }
        
        .edit-btn {
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }
        
        .profile-field:hover .edit-btn {
            opacity: 1;
        }
        
        .profile-picture-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 2rem;
        }

        .profile-picture {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        .profile-picture-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(0,0,0,0.5);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .profile-picture-container:hover .profile-picture-overlay {
            opacity: 1;
        }

        .loading {
            position: relative;
            opacity: 0.7;
            pointer-events: none;
        }

        .preset-image-card {
            position: relative;
            cursor: pointer;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .preset-image-card:hover {
            transform: scale(1.05);
        }

        .preset-image-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .preset-image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .preset-image-card:hover .preset-image-overlay {
            opacity: 1;
        }
    </style>                                                                                         
</head>
<body>
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">My Profile</h3>
                    </div>
                    
                    <div class="card-body">
                        <!-- Profile Picture Section -->
                        <div class="profile-picture-section mb-4">
                            <div class="profile-picture-container">
                                <img src="../../data/pfp/<?php echo htmlspecialchars($profile_picture); ?>" 
                                     alt="Profile Picture" 
                                     class="profile-picture">
                                <div class="profile-picture-overlay" data-bs-toggle="modal" data-bs-target="#profilePictureModal">
                                    <i class="bi bi-camera-fill text-white"></i>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Personal Information Section -->
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">Personal Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="profile-field">
                                            <label class="text-muted small">First Name</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-name"><?php echo htmlspecialchars($client_data['name']); ?></span>
                                                <button class="btn btn-link btn-sm edit-btn ms-2" data-field="name">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="profile-field">
                                            <label class="text-muted small">Last Name</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-lastname"><?php echo htmlspecialchars($client_data['lastname']); ?></span>
                                                <button class="btn btn-link btn-sm edit-btn ms-2" data-field="lastname">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="profile-field">
                                            <label class="text-muted small">Username</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-username"><?php echo htmlspecialchars($_SESSION['client_username']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contact Information Section -->
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">Contact Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="profile-field">
                                            <label class="text-muted small">Phone</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-phone"><?php echo htmlspecialchars($client_data['phone']); ?></span>
                                                <button class="btn btn-link btn-sm edit-btn ms-2" data-field="phone">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-field">
                                            <label class="text-muted small">Company</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-company"><?php echo htmlspecialchars($client_data['company']); ?></span>
                                                <button class="btn btn-link btn-sm edit-btn ms-2" data-field="company">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Address Section -->
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">Address</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="profile-field">
                                            <label class="text-muted small">Street</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-street"><?php echo htmlspecialchars($client_data['street']); ?></span>
                                                <button class="btn btn-link btn-sm edit-btn ms-2" data-field="street">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="profile-field">
                                            <label class="text-muted small">Number</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-number"><?php echo htmlspecialchars($client_data['number']); ?></span>
                                                <button class="btn btn-link btn-sm edit-btn ms-2" data-field="number">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="profile-field">
                                            <label class="text-muted small">District</label>
                                            <div class="d-flex align-items-center">
                                                <span id="display-colony"><?php echo htmlspecialchars($client_data['colony']); ?></span>
                                                <button class="btn btn-link btn-sm edit-btn ms-2" data-field="colony">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="fieldName">
                        <div class="mb-3">
                            <label for="fieldValue" class="form-label" id="fieldLabel"></label>
                            <input type="text" class="form-control" id="fieldValue">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <!-- Profile Picture Modal -->
    <div class="modal fade" id="profilePictureModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#preset-images">Preset Images</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#custom-upload">Custom Upload</a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <!-- Preset Images Tab -->
                        <div class="tab-pane fade show active" id="preset-images">
                            <div class="row g-3">
                                <?php
                                $preset_images = ['capybara.jpg', 'panda.jpg', 'cat.jpg', 'dog.jpg'];
                                foreach ($preset_images as $image) {
                                    $image_name = pathinfo($image, PATHINFO_FILENAME);
                                ?>
                                <div class="col-6 col-md-3">
                                    <div class="preset-image-card" data-image="<?php echo $image; ?>">
                                        <img src="../../data/pfp/presets/<?php echo $image; ?>" 
                                             class="img-fluid rounded" 
                                             alt="<?php echo ucfirst($image_name); ?>">
                                        <div class="preset-image-overlay">
                                            <span class="preset-image-name"><?php echo ucfirst($image_name); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <!-- Custom Upload Tab -->
                        <div class="tab-pane fade" id="custom-upload">
                            <div class="text-center p-4">
                                <i class="bi bi-cloud-upload display-4 mb-3"></i>
                                <h5>Upload Your Own Image</h5>
                                <p class="text-muted">Maximum file size: 5MB</p>
                                <input type="file" 
                                       id="profile-picture-input" 
                                       accept="image/*" 
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        const toast = new bootstrap.Toast(document.getElementById('toast'));
        
        // Profile picture upload handling
        document.getElementById('profile-picture-input').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file type and size
            if (!file.type.startsWith('image/')) {
                document.querySelector('.toast-body').textContent = 'Please select an image file';
                toast.show();
                return;
            }

            if (file.size > 5 * 1024 * 1024) { // 5MB limit
                document.querySelector('.toast-body').textContent = 'Image must be less than 5MB';
                toast.show();
                return;
            }

            const formData = new FormData();
            formData.append('profile_picture', file);

            try {
                const response = await fetch('../../app/Handlers/updatePfp.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                console.log('Response:', data); // Para debug

                if (data.success) {
                    // Update all profile pictures on the page
                    const profilePics = document.querySelectorAll('.profile-picture, .dropdown .rounded-circle');
                    profilePics.forEach(pic => {
                        pic.src = `../../data/pfp/${data.filename}?t=${Date.now()}`;
                    });
                    document.querySelector('.toast-body').textContent = 'Profile picture updated successfully!';
                } else {
                    document.querySelector('.toast-body').textContent = data.message || 'Update failed';
                }
            } catch (error) {
                console.error('Error:', error);
                document.querySelector('.toast-body').textContent = 'An error occurred. Please try again.';
            }
            toast.show();
        });

        // Handle edit buttons
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const field = this.dataset.field;
                const currentValue = document.getElementById(`display-${field}`).textContent.trim();
                
                document.getElementById('fieldName').value = field;
                document.getElementById('fieldValue').value = currentValue;
                document.getElementById('fieldLabel').textContent = 
                    this.closest('.profile-field').querySelector('label').textContent;
                
                editModal.show();
            });
        });

        // Handle save changes
        document.getElementById('saveChanges').addEventListener('click', async function() {
            const field = document.getElementById('fieldName').value;
            const value = document.getElementById('fieldValue').value;
            const displayElement = document.getElementById(`display-${field}`);
            const profileField = displayElement.closest('.profile-field');
            
            if (!value.trim()) {
                document.querySelector('.toast-body').textContent = 'Please enter a value';
                toast.show();
                return;
            }
            
            profileField.classList.add('loading');
            
            try {
                const response = await fetch('../../app/Controllers/updateProfile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        field: field,
                        value: value.trim()
                    }),
                });
                
                const data = await response.json();
                
                if (data.success) {
                    displayElement.textContent = value.trim();
                    document.querySelector('.toast-body').textContent = 'Updated successfully!';
                    editModal.hide();
                } else {
                    document.querySelector('.toast-body').textContent = data.message || 'Update failed';
                }
            } catch (error) {
                console.error('Error:', error);
                document.querySelector('.toast-body').textContent = 'An error occurred. Please try again.';
            } finally {
                profileField.classList.remove('loading');
                toast.show();
            }
        });

        // Handle modal form submission
        document.getElementById('editForm').addEventListener('submit', (e) => {
            e.preventDefault();
            document.getElementById('saveChanges').click();
        });

        // Handle preset image selection
        document.querySelectorAll('.preset-image-card').forEach(card => {
            card.addEventListener('click', async function() {
                const imageName = this.dataset.image;
                const formData = new FormData();
                formData.append('preset_image', imageName);

                try {
                    const response = await fetch('../../app/Handlers/updatePfp.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        // Update all profile pictures on the page
                        const profilePics = document.querySelectorAll('.profile-picture, .dropdown .rounded-circle');
                        profilePics.forEach(pic => {
                            pic.src = `../../data/pfp/${data.filename}?t=${Date.now()}`;
                        });
                        document.querySelector('.toast-body').textContent = 'Profile picture updated successfully!';
                        bootstrap.Modal.getInstance(document.getElementById('profilePictureModal')).hide();
                        // Resetear el input de archivo
                        document.getElementById('profile-picture-input').value = '';
                    } else {
                        document.querySelector('.toast-body').textContent = data.message || 'Update failed';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    document.querySelector('.toast-body').textContent = 'An error occurred. Please try again.';
                }
                toast.show();
            });
        });
    });
    </script>
</body>
</html>
