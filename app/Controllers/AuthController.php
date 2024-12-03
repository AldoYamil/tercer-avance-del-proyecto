<?php
include_once '../../config/connection.php'; 

session_start();

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $sql = "SELECT num, username, password, role FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verificación simple de contraseña sin hash
        if ($username == $row['username'] && $_POST['password'] == $row['password']) {
            $_SESSION['num'] = $row['num'];
            $_SESSION['username'] = $row['username']; 
            $_SESSION['role'] = $row['role'];

            if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
                // Set secure cookies with path and domain
                setcookie('employee_username', $username, [
                    'expires' => time() + (30 * 24 * 60 * 60),
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
                setcookie('employee_id', $row['num'], [
                    'expires' => time() + (30 * 24 * 60 * 60),
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
            } else {
                // Clear cookies if remember me is not checked
                setcookie('employee_username', '', [
                    'expires' => time() - 3600,
                    'path' => '/'
                ]);
                setcookie('employee_id', '', [
                    'expires' => time() - 3600,
                    'path' => '/'
                ]);
            }
            switch ($row['role']) {
                case 'R001': 
                    header("Location: ../../views/supervisor/supervisorDashboard.php");
                    exit();
                case 'R002': 
                    header("Location: ../../views/operator/operatorDashboard.php");
                    exit();
                case 'R003': 
                    header("Location: ../../views/transportist/transportistDashboard.php");
                    exit();
                case 'R005': 
                    header("Location: ../../views/admin/adminDashboard.php");
                    exit();
                default:
                    header("Location: ../../views/auth/LoginViewEmployee.php?error=Acesso denegado");
            }
        } else {
            header("Location: ../../views/auth/LoginViewEmployee.php?error=Credenciales incorrectas");
            exit();
        }
    } else {
        header("Location: ../../views/auth/LoginViewEmployee.php?error=Usuario no encontrado");
        exit();
    }
} else {
    echo "Por favor, ingrese su nombre de usuario.";
}

$conn->close();
?>
