--O is for ONGC employee(Store Keeper and Collector) and G is for Security Guard
CREATE TABLE employee (
  cpfno INT NOT NULL,
  empname VARCHAR(30) NOT NULL,
  designation ENUM('O','G'),  
  PRIMARY KEY (cpfno)
);