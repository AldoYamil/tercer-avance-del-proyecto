<?php
include '../../config/connection.php';

if (isset($_POST['name'], $_POST['lastname'], $_POST['username'], $_POST['password'])) {
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $surname = $_POST['surname'] ?? null;
    $company = $_POST['company'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $street = $_POST['street'] ?? null;
    $colony = $_POST['colony'] ?? null;
    $number = $_POST['number'] ?? null;
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role_code = "R004";

    $sql_check_user = "CALL CheckUserExists(?, @user_count)";
    $stmt_check_user = $conn->prepare($sql_check_user);
    $stmt_check_user->bind_param("s", $username);
    $stmt_check_user->execute();

    $result = $conn->query("SELECT @user_count AS total");
    $row_check = $result->fetch_assoc();

    if ($row_check['total'] > 0) {
        header("Location: ../../views/auth/LoginViewUser.php?error=El nombre de usuario '$username' ya está en uso. Por favor, elige otro.");
        exit();
    }

    $sql_insert_user = "CALL InsertUserC(?, ?, ?, @new_user_id)";
    $stmt_user = $conn->prepare($sql_insert_user);
    $stmt_user->bind_param("sss", $username, $password, $role_code);

    if ($stmt_user->execute()) {
        $result = $conn->query("SELECT @new_user_id AS user_num");
        $row_user = $result->fetch_assoc();
        $user_num = $row_user['user_num'];

        $sql_insert_client = "CALL InsertClient(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_client = $conn->prepare($sql_insert_client);
        $stmt_client->bind_param("ssssssssiss", $name, $lastname, $surname, $company, $phone, $street, $colony, $number, $user_num, $username, $password);

        if ($stmt_client->execute()) {
            header("Location: ../../views/auth/LoginViewUser.php?success=Usuario registrado exitosamente. Por favor inicie sesión.");
            exit();
        } else {
            header("Location: ../../views/auth/LoginViewUser.php?error=Error al registrar el cliente: " . $stmt_client->error);
            exit();
        }
    } else {
        header("Location: ../../views/auth/LoginViewUser.php?error=Error al registrar el usuario: " . $stmt_user->error);
        exit();
    }
} else {
    header("Location: ../../views/auth/LoginViewUser.php?error=Por favor, complete todos los campos obligatorios.");
    exit();
}