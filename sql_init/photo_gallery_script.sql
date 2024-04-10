DROP DATABASE db_gallery;

CREATE DATABASE db_gallery;

USE db_gallery;

CREATE TABLE tbl_images(
id int auto_increment not null primary key, 
image_filename varchar(255) not null, 
image_type varchar(50) not null, 
image_title varchar(60),
image_description varchar(255));