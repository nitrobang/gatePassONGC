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
alter table order_no modify column securityn varchar(150);
alter table order_no modify column collectorid varchar(20);

alter table order_no modify column security_approval tinyint(1) signed DEFAULT 0 NOT NULL;
alter table order_no modify column coll_approval tinyint(1) signed DEFAULT 0 NOT NULL;