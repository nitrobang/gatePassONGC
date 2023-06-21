CREATE TABLE user_to_groups (
    user_id INT NOT NULL,
    group_id INT NOT NULL,
    PRIMARY KEY (user_id, group_id),
    FOREIGN KEY (user_id) REFERENCES external_users (id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES user_groups (group_id) ON DELETE CASCADE
);


insert into user_to_groups values(6, 1);
insert into user_to_groups values(6, 3);
insert into user_to_groups values(7, 1);
insert into user_to_groups values(7, 1);
insert into user_to_groups values(7, 2);
insert into user_to_groups values(8, 1);
insert into user_to_groups values(9, 1);
insert into user_to_groups values(9, 2);

