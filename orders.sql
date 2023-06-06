CREATE TABLE orders (
  descrip VARCHAR(1000) NOT NULL,
  nop INT NOT NULL,
  deliverynote VARCHAR(1000),
  remark VARCHAR(1000),
  orderno INT NOT NULL,
  PRIMARY KEY (orderno)
);

ALTER TABLE orders
ADD CONSTRAINT fk_order_no
FOREIGN KEY (orderno) REFERENCES order_no(orderno);

