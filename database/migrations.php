<?php

return [
    'CREATE TABLE IF NOT EXISTS `users` (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `first_name` VARCHAR(30) NOT NULL,
        `last_name` VARCHAR(30) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT NULL,
        PRIMARY KEY (`id`) USING BTREE
    )',
    'CREATE TABLE tasks (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `user_id` INT(10) UNSIGNED,
        `points` INT DEFAULT 0,
        `status` ENUM("pending", "completed") DEFAULT "pending",
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT NULL,
        PRIMARY KEY (`id`) USING BTREE,
        FOREIGN KEY (`user_id`) REFERENCES users(`id`)
    );'
];
