<?php
session_start();
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'];
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $surname = $_POST['surname'];
    $company = $_POST['company'];
    $phone = $_POST['phone'];
    $street = $_POST['street'];
    $colony = $_POST['colony'];
    $number = $_POST['number'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn->begin_transaction();

    try {
        // Actualización de los datos del cliente
        $sql_client_update = "UPDATE Client SET name = ?, lastname = ?, surname = ?, company = ?, phone = ?, street = ?, colony = ?, number = ? WHERE num = ?";
        $stmt_client_update = $conn->prepare($sql_client_update);
        $stmt_client_update->bind_param("ssssssssi", $name, $lastname, $surname, $company, $phone, $street, $colony, $number, $client_id);
        $stmt_client_update->execute();

        // Actualización de los datos del usuario (con encriptación de la contraseña si se proporciona)
        $sql_user_update = "UPDATE Users SET username = ?";
        
        if (!empty($password)) {
            // Si se proporciona una nueva contraseña, encriptarla con AES_ENCRYPT
            $encrypted_password = openssl_encrypt($password, 'AES-128-ECB', 'clave_secreta');
            $sql_user_update .= ", password = ?";
            $stmt_user_update = $conn->prepare($sql_user_update . " WHERE num = (SELECT usernum FROM Client WHERE num = ?)");
            $stmt_user_update->bind_param("ssi", $username, $encrypted_password, $client_id);
        } else {
            $stmt_user_update = $conn->prepare($sql_user_update . " WHERE num = (SELECT usernum FROM Client WHERE num = ?)");
            $stmt_user_update->bind_param("si", $username, $client_id);
        }

        $stmt_user_update->execute();

        $conn->commit();

        header("Location: ../../views/client/clientDashboard.php?message=Usuario actualizado exitosamente");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error al actualizar la información: " . $e->getMessage();
    }
} else {
    echo "Método no permitido.";
}
?>
