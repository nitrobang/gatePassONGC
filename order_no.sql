CREATE TABLE order_no (
  orderno INT NOT NULL AUTO_INCREMENT,
  order_dest VARCHAR(30) NOT NULL,
  issue_desc VARCHAR(1000) NOT NULL,
  placeoi VARCHAR(1000) NOT NULL,
  issueto VARCHAR(1000) NOT NULL,
  securityn VARCHAR(1000) NOT NULL,
  collectorid VARCHAR(1000) NOT NULL,
  returnable TINYINT(1) NOT NULL,
  coll_approval TINYINT(1) NOT NULL DEFAULT 0,
  security_approval TINYINT(1) NOT NULL DEFAULT 0,
  guard_approval TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (orderno)
);
