CREATE TABLE employee (
  cpfno INT NOT NULL,
  empname VARCHAR(100) NOT NULL,
  designation ENUM('E','S') NOT NULL, 
  venue ENUM('N', 'V', 'H') NOT NULL, 
  PRIMARY KEY (cpfno)
);

INSERT INTO employee (cpfno, empname, designation, venue) VALUES
(123456, 'John Doe', 'E', 'N'),
(789012, 'Jane Smith', 'G', 'V'),
(345678, 'Michael Johnson', 'E', 'H'),
(901234, 'Emily Davis', 'G', 'N'),
(567890, 'David Wilson', 'E', 'V');
