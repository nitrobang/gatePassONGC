CREATE TABLE user_to_groups (
  id INT,
  group_id INT,
  PRIMARY KEY (id, group_id),
  FOREIGN KEY (id) REFERENCES users(id),
  FOREIGN KEY (group_id) REFERENCES user_groups(group_id)
);

insert into user_to_groups values(6, 1);
insert into user_to_groups values(6, 3);
insert into user_to_groups values(7, 1);
insert into user_to_groups values(7, 1);
insert into user_to_groups values(7, 2);
insert into user_to_groups values(8, 1);
insert into user_to_groups values(9, 1);
insert into user_to_groups values(9, 2);

