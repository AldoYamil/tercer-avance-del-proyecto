DELIMITER //

CREATE TRIGGER generate_email_before_insert
BEFORE INSERT ON Employees
FOR EACH ROW
BEGIN
    DECLARE generated_email VARCHAR(50);
    SET generated_email = CONCAT(LOWER(SUBSTRING(NEW.name, 1, 2)), 
                                 LOWER(NEW.lastname), 
                                 LOWER(NEW.surname), 
                                 '@domain.com');
    SET NEW.email = generated_email;
END;
//


CREATE TRIGGER Before_Insert_Shipment
BEFORE INSERT ON Shipment
FOR EACH ROW
BEGIN
    SET NEW.tracking_code = CONCAT(DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));
END //

DELIMITER //

CREATE TRIGGER generate_employee_number
BEFORE INSERT ON Employees
FOR EACH ROW
BEGIN
    DECLARE random_number VARCHAR(10);
    REPEAT
        SET random_number = LPAD(FLOOR(RAND() * 10000000000), 10, '0');
    UNTIL NOT EXISTS (SELECT 1 FROM Employees WHERE num_employee = random_number)
    END REPEAT;
    SET NEW.num_employee = random_number;
END;
 //



DELIMITER //

CREATE TRIGGER UpdateStockOnPackageInsert
AFTER INSERT ON Package
FOR EACH ROW
BEGIN
    UPDATE Stock
    SET amount = amount - NEW.amount
    WHERE code = NEW.stock;

    IF (SELECT amount FROM Stock WHERE code = NEW.stock) < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Not enough stock';
    END IF;
END;
//

DELIMITER ;
