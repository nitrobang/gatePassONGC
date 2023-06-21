CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cpfno INT NOT NULL, 
    username VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE users
ADD CONSTRAINT fk_employee_cpfno FOREIGN KEY (cpfno)
REFERENCES employee(cpfno);

