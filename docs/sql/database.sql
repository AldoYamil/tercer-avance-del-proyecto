DROP DATABASE IF EXISTS Cafpath;
CREATE DATABASE IF NOT EXISTS Cafpath;

USE Cafpath;

CREATE TABLE IF NOT EXISTS Path (
    num INT PRIMARY KEY AUTO_INCREMENT, 
    starting_point VARCHAR(100) NOT NULL,
    end_point VARCHAR(100) NOT NULL,
    est_arrival DATE NOT NULL,
    starting_date DATE NOT NULL,
    id_ruta INT,
    status VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS Warehouse (
    code VARCHAR(6) PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    street VARCHAR(50) NOT NULL,
    colony VARCHAR(50) NOT NULL,
    number VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS Item (
    code VARCHAR(6) PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS Insurance (
    num INT PRIMARY KEY AUTO_INCREMENT,
    policyNumber VARCHAR(50) NOT NULL,
    insurance_type VARCHAR(100),
    coverage DECIMAL(10,2)
);

CREATE TABLE IF NOT EXISTS Role (
    code VARCHAR(6) PRIMARY KEY,
    name VARCHAR(20) NOT NULL,
    privileges VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS Users (
    num INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(100) NOT NULL,
    role VARCHAR(6) NOT NULL,
    profile_picture VARCHAR(100) DEFAULT 'default.jpg',
    FOREIGN KEY (role) REFERENCES Role(code)
);

CREATE TABLE IF NOT EXISTS Client (
    num INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL,
    lastname VARCHAR(20) NOT NULL,
    surname VARCHAR(20),
    company VARCHAR(20),
    phone VARCHAR(15),
    street VARCHAR(35),
    colony VARCHAR(35),
    number VARCHAR(35),
    usernum INT NOT NULL,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(50),
    FOREIGN KEY (usernum) REFERENCES Users(num)
);

CREATE TABLE IF NOT EXISTS Stock (
    code VARCHAR(6) PRIMARY KEY,
    name VARCHAR(100),
    amount INT NOT NULL,
    description VARCHAR(255),
    warehouse VARCHAR(6),
    FOREIGN KEY (warehouse) REFERENCES Warehouse(code)
);

CREATE TABLE IF NOT EXISTS Incident (
    num INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100),
    description VARCHAR(255) NOT NULL,
    status VARCHAR(100),
    date_incident DATE NOT NULL,
    date_report TIMESTAMP NOT NULL,
    user INT NOT NULL,
    FOREIGN KEY (user) REFERENCES Users(num)
);

CREATE TABLE IF NOT EXISTS Vehicle (
    number INT PRIMARY KEY AUTO_INCREMENT,
    license_plate VARCHAR(15) NOT NULL,
    model VARCHAR(20) NOT NULL,
    max_capacity DECIMAL(10, 2) NOT NULL, 
    status VARCHAR(20),
    warehouse VARCHAR(6),
    FOREIGN KEY (warehouse) REFERENCES Warehouse(code)
);

CREATE TABLE IF NOT EXISTS Employees (
    num INT PRIMARY KEY AUTO_INCREMENT,
    num_employee VARCHAR(20),
    name VARCHAR(20) NOT NULL,
    lastname VARCHAR(20) NOT NULL,
    surname VARCHAR(20),
    role VARCHAR(15) NOT NULL,
    usernum INT NOT NULL,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(100) NOT NULL,
    status VARCHAR(20),
    warehouse VARCHAR(6),
    email VARCHAR(50),
    FOREIGN KEY (usernum) REFERENCES Users(num),
    FOREIGN KEY (warehouse) REFERENCES Warehouse(code)
);

CREATE TABLE IF NOT EXISTS Vehicle_Assignment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_number INT NOT NULL,
    employee_num INT NOT NULL,
    assigned_date DATE NOT NULL,
    FOREIGN KEY (vehicle_number) REFERENCES Vehicle(number),
    FOREIGN KEY (employee_num) REFERENCES Employees(num)
);

CREATE TABLE IF NOT EXISTS Shipment (
    num INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL,
    delivery_date DATE NOT NULL,
    client INT NOT NULL,
    incident INT,
    vehicle INT,
    path INT,
    insurance INT NOT NULL,
    warehouse VARCHAR(6),
    tracking_code VARCHAR(20) UNIQUE,
    FOREIGN KEY (client) REFERENCES Client(num),
    FOREIGN KEY (incident) REFERENCES Incident(num),
    FOREIGN KEY (vehicle) REFERENCES Vehicle(number),
    FOREIGN KEY (path) REFERENCES Path(num),
    FOREIGN KEY (insurance) REFERENCES Insurance(num),
    FOREIGN KEY (warehouse) REFERENCES Warehouse(code)
);

CREATE TABLE IF NOT EXISTS Record (
    num INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL,
    location VARCHAR(100),
    status VARCHAR(20),
    client INT,
    shipment INT NOT NULL,
    FOREIGN KEY (client) REFERENCES Client(num),
    FOREIGN KEY (shipment) REFERENCES Shipment(num)
);

CREATE TABLE IF NOT EXISTS Assamble (
    employees INT NOT NULL,
    shipment INT NOT NULL,
    status VARCHAR(20),
    PRIMARY KEY (employees, shipment),
    FOREIGN KEY (employees) REFERENCES Employees(num),
    FOREIGN KEY (shipment) REFERENCES Shipment(num)
);

CREATE TABLE IF NOT EXISTS Package (
    shipment INT NOT NULL, 
    stock VARCHAR(6) NOT NULL,
    amount INT NOT NULL,
    PRIMARY KEY (shipment, stock),
    FOREIGN KEY (shipment) REFERENCES Shipment(num),
    FOREIGN KEY (stock) REFERENCES Stock(code)
);

CREATE TABLE IF NOT EXISTS Inventory (
    stock VARCHAR(6) NOT NULL,
    item VARCHAR(6) NOT NULL,
    PRIMARY KEY (stock, item),
    FOREIGN KEY (item) REFERENCES Item(code),
    FOREIGN KEY (stock) REFERENCES Stock(code)
);

CREATE TABLE IF NOT EXISTS RutaDetalles (
    ruta INT PRIMARY KEY AUTO_INCREMENT,
    id_vehiculo INT NOT NULL,
    fecha DATE NOT NULL,
    orden_entrega INT NOT NULL,
    status VARCHAR(50),
    id_paquete INT NOT NULL,
    direccion_destino VARCHAR(255) NOT NULL,
    id_ruta INT,
    FOREIGN KEY (id_vehiculo) REFERENCES Vehicle(number),
    FOREIGN KEY (id_paquete) REFERENCES Shipment(num),
    FOREIGN KEY (id_ruta) REFERENCES Path (num)
);

CREATE TABLE StockMovement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stock VARCHAR(50) NOT NULL,
    warehouse VARCHAR(50) NOT NULL,
    type ENUM('IN', 'OUT') NOT NULL,
    amount INT NOT NULL,
    reason TEXT NOT NULL,
    operator INT NOT NULL,
    date DATETIME NOT NULL,
    FOREIGN KEY (stock) REFERENCES Stock(code),
    FOREIGN KEY (warehouse) REFERENCES Warehouse(code),
    FOREIGN KEY (operator) REFERENCES Employees(num)
);