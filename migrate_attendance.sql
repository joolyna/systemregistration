-- Run this ONCE in phpMyAdmin if you already have the registrations table.
-- (Fresh install? Just import database.sql instead.)
-- Adds the Attendance column (Pending / Present / Absent / Excused).
-- Old "Attended" registrations become Registered + Present, so no data is lost.
USE student_event_registration;

ALTER TABLE registrations
  ADD COLUMN attendance ENUM('Pending','Present','Absent','Excused') NOT NULL DEFAULT 'Pending' AFTER status;

UPDATE registrations SET attendance = 'Present' WHERE status = 'Attended';
UPDATE registrations SET status = 'Registered' WHERE status = 'Attended';

ALTER TABLE registrations
  MODIFY status ENUM('Registered','Cancelled') NOT NULL DEFAULT 'Registered';
