-- Run this ONCE in phpMyAdmin if you already have the database.
-- (Fresh install? Just import database.sql instead.)
-- Adds the users table used by the Login / Register pages.
USE student_event_registration;

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
