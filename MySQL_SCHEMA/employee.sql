--O is for ONGC employee(Store Keeper and Collector) and G is for Security Guard
CREATE TABLE employee (
  cpfno INT NOT NULL,
  empname VARCHAR(100) NOT NULL,
  designation ENUM('O','G') NOT NULL, 
  venue ENUM('N', 'V', 'H') NOT NULL, 
  PRIMARY KEY (cpfno)
);

INSERT INTO employee (cpfno, empname, designation, venue) VALUES
(123456, 'John Doe', 'O', 'N'),
(789012, 'Jane Smith', 'G', 'V'),
(345678, 'Michael Johnson', 'O', 'H'),
(901234, 'Emily Davis', 'G', 'N'),
(567890, 'David Wilson', 'O', 'V');
