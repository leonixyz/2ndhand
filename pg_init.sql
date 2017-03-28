create database example;
create role user with password 'bingo';
create database example;
grant all privileges on database example to user;
alter role example with login;