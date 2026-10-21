CREATE DATABASE IF NOT EXISTS student_event_registration
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_event_registration;

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
  student_id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(120) NOT NULL,
  course VARCHAR(80) NOT NULL,
  year_level VARCHAR(30) NOT NULL,
  email VARCHAR(150) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS courses (
  course_id INT AUTO_INCREMENT PRIMARY KEY,
  college VARCHAR(100) NOT NULL,
  course_name VARCHAR(80) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
  event_id INT AUTO_INCREMENT PRIMARY KEY,
  event_name VARCHAR(150) NOT NULL,
  event_date DATE NOT NULL,
  venue VARCHAR(150) DEFAULT NULL,
  capacity INT NOT NULL DEFAULT 50,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS registrations (
  registration_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  event_id INT NOT NULL,
  status ENUM('Registered','Cancelled') NOT NULL DEFAULT 'Registered',
  attendance ENUM('Pending','Present','Absent','Excused') NOT NULL DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_registration_student FOREIGN KEY (student_id)
    REFERENCES students(student_id) ON DELETE CASCADE,
  CONSTRAINT fk_registration_event FOREIGN KEY (event_id)
    REFERENCES events(event_id) ON DELETE CASCADE
);

INSERT IGNORE INTO courses (college, course_name) VALUES
('College of Business Education (CBE)','Accountancy'),
('College of Business Education (CBE)','Accounting Technology'),
('College of Business Education (CBE)','Business Administration (Financial Management)'),
('College of Business Education (CBE)','Business Management'),
('College of Business Education (CBE)','Hospitality Management'),
('College of Business Education (CBE)','Tourism'),
('College of Teacher Education (CTE)','Bachelor of Elementary Education'),
('College of Teacher Education (CTE)','Bachelor of Secondary Education major in English'),
('College of Teacher Education (CTE)','Bachelor of Secondary Education major in Mathematics'),
('College of Teacher Education (CTE)','Bachelor of Secondary Education major in Science'),
('College of Teacher Education (CTE)','Bachelor of Secondary Education major in Social Studies'),
('College of Computer Studies (CCS)','Computer Science'),
('College of Computer Studies (CCS)','Information Technology'),
('College of Computer Studies (CCS)','Associate in Computer Technology'),
('College of Criminal Justice Education (CCJE)','Bachelor of Science in Criminology'),
('Arts and Sciences / Psychology','Bachelor of Science in Psychology');

INSERT INTO students (student_name, course, year_level, email) VALUES
('Alex Santos','Information Technology','3rd Year','alex@example.com'),
('Jamie Cruz','Computer Science','2nd Year','jamie@example.com');

INSERT INTO events (event_name, event_date, venue, capacity) VALUES
('IT Week 2026','2026-11-10','Main Auditorium',100),
('Career Orientation','2026-11-15','Room 201',2);

INSERT INTO registrations (student_id, event_id, status) VALUES
(1,1,'Registered'),
(2,2,'Registered');
