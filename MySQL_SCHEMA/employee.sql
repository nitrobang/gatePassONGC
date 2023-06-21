--O is for ONGC employee(Store Keeper and Collector) and G is for Security Guard
CREATE TABLE employee (
  cpfno INT NOT NULL,
  empname VARCHAR(100) NOT NULL,
  designation ENUM('E','S') NOT NULL, 
  venue ENUM('N', 'V', 'H') NOT NULL, 
  PRIMARY KEY (cpfno)
);

INSERT INTO employee (cpfno, empname, designation, venue) VALUES
(123456, 'John Doe', 'E', 'N'),
(789012, 'Jane Smith', 'S', 'V'),
(345678, 'Michael Johnson', 'E', 'H'),
(901234, 'Emily Davis', 'S', 'N'),
(567890, 'David Wilson', 'E', 'V');
