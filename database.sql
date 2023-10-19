show databases;
use laravel_database;
create table products
(
	id varchar(100) not null primary key,
    name varchar(100) not null,
    description text,
    price int not null,
    category_id varchar(100) not null,
    created_at timestamp not null default current_timestamp,
    constraint fk_category_id foreign key (category_id) references categories (id)
)engine innodb;