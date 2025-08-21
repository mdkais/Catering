--DataBase Name: kitchen

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


--TABLE NO. 1
CREATE TABLE `users`(
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `phone` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `comment` varchar(255)
);

INSERT INTO `users`(id,username,password,phone,email,comment) VALUES
(1,'admin','admin','123467890','admin@xyz.com','NaN');


--TABLE NO. 2
CREATE TABLE `order_items`(
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `order_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL,
    `total_price` decimal(10,2) NOT NULL
);


--TABLE NO. 3
CREATE TABLE `orders`(
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` int(11) NOT NULL,
    `order_date`  NOT NULL DEFAULT current_timestamp(),
    `total_amount` decimal(10,2) NOT NULL
);


--TABLE NO. 4
CREATE TABLE `products`(
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(255) NOT NULL,
    `price` decimal(10,2) NOT NULL
);

INSERT INTO `products`(id,name,price) VALUES
(1,'Biryani',800),
(1,'Paneer',500),
(1,'VEG',200);