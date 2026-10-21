-- Run this ONCE in phpMyAdmin if you already imported the old database.sql
-- and want to keep your existing data. (Fresh install? Just import database.sql instead.)
USE student_event_registration;

CREATE TABLE IF NOT EXISTS events (
  event_id INT AUTO_INCREMENT PRIMARY KEY,
  event_name VARCHAR(150) NOT NULL,
  event_date DATE NOT NULL,
  venue VARCHAR(150) DEFAULT NULL,
  capacity INT NOT NULL DEFAULT 50,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Turn every distinct event already typed into registrations into a real event
INSERT INTO events (event_name, event_date, capacity)
SELECT event_name, event_date, 50 FROM registrations GROUP BY event_name, event_date;

ALTER TABLE registrations ADD COLUMN event_id INT NULL AFTER student_id;

UPDATE registrations r
JOIN events e ON e.event_name = r.event_name AND e.event_date = r.event_date
SET r.event_id = e.event_id;

ALTER TABLE registrations
  MODIFY event_id INT NOT NULL,
  DROP COLUMN event_name,
  DROP COLUMN event_date,
  ADD CONSTRAINT fk_registration_event FOREIGN KEY (event_id)
    REFERENCES events(event_id) ON DELETE CASCADE;
