CREATE TABLE order_no (
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  orderno INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_dest VARCHAR(100) NOT NULL,
  issue_desc VARCHAR(1000) NOT NULL,
  placeoi ENUM('N', 'V', 'H') NOT NULL,
  issueto VARCHAR(150) NOT NULL,
  securityn VARCHAR(300),
  guard_name VARCHAR(300),
  collector_name VARCHAR(300),
  returnable TINYINT(1) NOT NULL,
  coll_approval TINYINT(1) signed NOT NULL DEFAULT 0,
  security_approval TINYINT(1) signed NOT NULL DEFAULT 0,
  guard_approval TINYINT(1) signed NOT NULL DEFAULT 0,
  comp_approval TINYINT(1) NOT NULL DEFAULT 0,
  forwarded_to INT NOT NULL,
  moc VARCHAR(100),
  vehno VARCHAR(10),
  created_by INT NOT NULL
);
ALTER TABLE order_no AUTO_INCREMENT = 1;