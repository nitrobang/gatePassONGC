create table user_groups( 
    id int primary key, 
    group_name varchar(255), 
    group_desc varchar(255)
);

insert into user_groups values(1, 'store_keeper', 'can create a new order');
insert into user_groups values(2, 'collector', 'collects the order and either approves or reverts');
insert into user_groups values(3, 'security', 'Final stage of checking, can approve or revert');