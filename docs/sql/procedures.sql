DELIMITER //

CREATE PROCEDURE CheckWarehouseCode(IN p_warehouse_code VARCHAR(6), OUT p_exists INT)
BEGIN
    SELECT COUNT(*) INTO p_exists FROM Warehouse WHERE code = p_warehouse_code;
END;
//

delimiter //

CREATE PROCEDURE GetMaxUserNum(OUT p_max_usernum INT)
BEGIN
    SELECT COALESCE(MAX(num), 0) INTO p_max_usernum FROM Users;
END;
//

delimiter //
CREATE PROCEDURE InsertUser(
    IN p_username VARCHAR(20), 
    IN p_password VARBINARY(255), 
    IN p_role VARCHAR(6),
    OUT generated_num INT
)
BEGIN
    INSERT INTO Users (username, password, role) 
    VALUES (p_username, p_password, p_role);
    SET generated_num = LAST_INSERT_ID();
END //
delimiter //
CREATE PROCEDURE InsertEmployee(
    IN p_name VARCHAR(20), 
    IN p_lastname VARCHAR(20), 
    IN p_surname VARCHAR(20), 
    IN p_role VARCHAR(15), 
    IN p_usernum INT, 
    IN p_username VARCHAR(20), 
    IN p_password VARBINARY(255), 
    IN p_status VARCHAR(20), 
    IN p_warehouse VARCHAR(6)
)
BEGIN
    INSERT INTO Employees (name, lastname, surname, role, usernum, username, password, status, warehouse)
    VALUES (p_name, p_lastname, p_surname, p_role, p_usernum, p_username, p_password, p_status, p_warehouse);
END //

delimiter //
CREATE PROCEDURE CheckUserExists(IN input_username VARCHAR(20), OUT user_count INT)
BEGIN
    SELECT COUNT(*) INTO user_count FROM Users WHERE username = input_username;
END;
//

delimiter //
CREATE PROCEDURE InsertUserC(IN input_username VARCHAR(20), IN input_password VARCHAR(20), IN input_role VARCHAR(6), OUT new_user_id INT)
BEGIN
    INSERT INTO Users (username, password, role) VALUES (input_username, input_password, input_role);
    SET new_user_id = LAST_INSERT_ID();
END;
//

delimiter //
CREATE PROCEDURE InsertClient(
    IN input_name VARCHAR(20), IN input_lastname VARCHAR(20), IN input_surname VARCHAR(20),
    IN input_company VARCHAR(20), IN input_phone VARCHAR(15), IN input_street VARCHAR(15),
    IN input_colony VARCHAR(15), IN input_number VARCHAR(15), IN input_usernum INT,
    IN input_username VARCHAR(20), IN input_password VARBINARY(255)
)
BEGIN
    INSERT INTO Client (name, lastname, surname, company, phone, street, colony, number, usernum, username, password)
    VALUES (input_name, input_lastname, input_surname, input_company, input_phone, input_street, input_colony, input_number, input_usernum, input_username, input_password);
END;
//

DELIMITER ;