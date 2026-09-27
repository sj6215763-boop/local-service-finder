CREATE DATABASE IF NOT EXISTS local_service_provider;
USE local_service_provider;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user','provider','admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    category_id INT NOT NULL,
    service_name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (provider_id) REFERENCES users(id)
        ON DELETE CASCADE,

    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE CASCADE
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    address VARCHAR(255) NOT NULL,
    status ENUM(
        'pending',
        'accepted',
        'rejected',
        'completed',
        'cancelled'
    ) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    FOREIGN KEY (service_id) REFERENCES services(id)
        ON DELETE CASCADE
);

INSERT INTO categories (name) VALUES
('Plumber'),
('Electrician'),
('Cleaning'),
('AC Repair'),
('Carpenter'),
('Painter'),
('Home Tutor'),
('Computer Repair');

-- Demo admin
-- Password: admin123
INSERT INTO users
(name, email, password, phone, role)
VALUES
(
    'Administrator',
    'admin@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC5i2lY6X1M3kM9w7i',
    '9999999999',
    'admin'
);