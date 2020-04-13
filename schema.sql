CREATE DATABASE readme;

USE readme;

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
email VARCHAR(128) NOT NULL UNIQUE,
login VARCHAR(128) NOT NULL UNIQUE,
password VARCHAR(128) NOT NULL,
avatar VARCHAR(128)
);

CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  title VARCHAR(128),
  content_text VARCHAR(512),
  quote_author VARCHAR(128),
  img VARCHAR(128),
  video VARCHAR(128),
  link VARCHAR(256),
  views INT UNSIGNED,
  
  user_id INT,
  type_id INT
);

CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  comment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  content VARCHAR(512),
  
  user_id INT,
  post_id INT
);

CREATE TABLE likes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  
  user_id INT,
  post_id INT
);

CREATE TABLE subscribtions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  
  user_id INT,
  userto_id INT
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  comment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  content VARCHAR(512),
  
  user_id INT,
  userto_id INT
);

CREATE TABLE hashtags (
id INT AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(128)
);

CREATE TABLE content_type (
id INT AUTO_INCREMENT PRIMARY KEY,
type_name VARCHAR(128) NOT NULL,
icon_type VARCHAR(128)
);

CREATE INDEX c_login ON users(login);
CREATE INDEX c_title ON posts(title);
