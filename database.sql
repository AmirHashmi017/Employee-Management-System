
CREATE DATABASE IF NOT EXISTS employee_management;
USE employee_management;

CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    position VARCHAR(100),
    salary DECIMAL(10, 2),
    hire_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    task_title VARCHAR(200) NOT NULL,
    task_description TEXT,
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

)
INSERT INTO admins (username, password, email) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@company.com');


INSERT INTO employees (name, email, phone, position, salary, hire_date) VALUES
('John Doe', 'john@company.com', '555-0101', 'Software Engineer', 75000.00, '2023-01-15'),
('Jane Smith', 'jane@company.com', '555-0102', 'Project Manager', 85000.00, '2022-06-20'),
('Mike Johnson', 'mike@company.com', '555-0103', 'Designer', 65000.00, '2023-03-10');


INSERT INTO tasks (employee_id, task_title, task_description, priority, status, deadline) VALUES
(1, 'Develop Login Module', 'Create secure authentication system', 'High', 'In Progress', '2025-11-15'),
(1, 'Bug Fixes', 'Fix reported issues in dashboard', 'Medium', 'Pending', '2025-11-20'),
(2, 'Project Planning', 'Plan Q4 project roadmap', 'High', 'Completed', '2025-11-05'),
(3, 'UI Redesign', 'Redesign company website', 'Medium', 'In Progress', '2025-12-01');