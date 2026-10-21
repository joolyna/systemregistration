-- Run this ONCE in phpMyAdmin if you already imported database.sql
-- and want the new Courses page. (Fresh install? Just import database.sql instead.)
USE student_event_registration;

CREATE TABLE IF NOT EXISTS courses (
  course_id INT AUTO_INCREMENT PRIMARY KEY,
  college VARCHAR(100) NOT NULL,
  course_name VARCHAR(80) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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

-- Keep any custom course already typed on a student
INSERT IGNORE INTO courses (college, course_name)
SELECT DISTINCT 'Other', course FROM students;
