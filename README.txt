STUDENT EVENT REGISTRATION SYSTEM - IM2 MIDTERM PROJECT

Requirements:
- XAMPP (Apache + MySQL)
- PHP 8+ with PDO
- phpMyAdmin

Setup:
1. Copy this folder into C:\xampp\htdocs\  (keep the folder name student_event_registration)
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and import database.sql.
   (Already imported an OLD database.sql and want to keep your data? Run migrate_events.sql once if you
   don't have the events table yet, then migrate_courses.sql once for the Courses page, and migrate_users.sql
   once for the Login / Register pages.)
4. If your database name or password is different, update config/connection.php.
5. Visit http://localhost/student_event_registration/ - you will be sent to the login page.
   Click "Create an account" to register your first user.

Features: dashboard, students, courses (manage the course dropdown), events (with seat capacity), and registrations
(all with add / edit / delete / search).
A student can't register twice for the same event, and full events are blocked.
Cancelled registrations don't use up a seat.
Statuses: Registered, Attended, Cancelled.
Look: espresso, cocoa and cream theme with a dark sidebar and light content area (no images).
Font: Plus Jakarta Sans (loaded from Google Fonts; falls back to system fonts offline).
Login: every page requires a login. Passwords are stored hashed (password_hash), forms use CSRF tokens,
and logout is a POST button in the sidebar.
