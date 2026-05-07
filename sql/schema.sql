CREATE DATABASE IF NOT EXISTS gestao_frota;
USE gestao_frota;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_code VARCHAR(20),
    plate VARCHAR(10) NOT NULL UNIQUE,
    internal_number VARCHAR(20),
    renavam VARCHAR(20),
    model VARCHAR(50),
    year INT,
    driver_name VARCHAR(100),
    driver_cpf VARCHAR(14),
    tank_capacity DECIMAL(10, 2),
    ipva_cost DECIMAL(10, 2) DEFAULT 0,
    insurance_cost DECIMAL(10, 2) DEFAULT 0,
    odometer INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS fuel_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    date DATE NOT NULL,
    liters DECIMAL(10, 2) NOT NULL,
    cost DECIMAL(10, 2) NOT NULL,
    odometer INT NOT NULL,
    km_per_liter DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS lava_rapido_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_datetime DATETIME NOT NULL,
    ec_name VARCHAR(150) NOT NULL,
    ec_city VARCHAR(100) NOT NULL,
    plate VARCHAR(10) NOT NULL,
    driver_name VARCHAR(100) NOT NULL,
    merchandise VARCHAR(150) NOT NULL,
    merchandise_quantity DECIMAL(10, 2) DEFAULT 0,
    merchandise_unit_value DECIMAL(10, 2) DEFAULT 0,
    transaction_value DECIMAL(10, 2) DEFAULT 0,
    previous_odometer INT DEFAULT 0,
    transaction_odometer INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS veloe_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_datetime DATETIME NOT NULL,
    ec_name VARCHAR(150) NOT NULL,
    ec_city VARCHAR(100) NOT NULL,
    plate VARCHAR(10) NOT NULL,
    driver_name VARCHAR(100) NOT NULL,
    merchandise VARCHAR(150) NOT NULL,
    merchandise_quantity DECIMAL(10, 2) DEFAULT 0,
    merchandise_unit_value DECIMAL(10, 2) DEFAULT 0,
    transaction_value DECIMAL(10, 2) DEFAULT 0,
    previous_odometer INT DEFAULT 0,
    transaction_odometer INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS manutencao_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    driver_name VARCHAR(100) NOT NULL,
    plate VARCHAR(10) NOT NULL,
    maintenance_type VARCHAR(150) NOT NULL,
    maintenance_value DECIMAL(10, 2) DEFAULT 0,
    maintenance_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
