<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/registration_validate.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Event.php';
require_once __DIR__ . '/classes/Registration.php';

$db = (new Database())->connect();
$regModel = new Registration($db);
$eventModel = new Event($db);

$students = $db->query("SELECT student_id, student_name, course FROM students ORDER BY student_name ASC")->fetchAll();

$events = $eventModel->getAll();

$error = '';
$values = ['student_id' => '', 'event_id' => '', 'status' => 'Registered'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$values, $error] = read_registration_post($db);

    if ($error === '') {
        try {
            $regModel->add((int) $values['student_id'], (int) $values['event_id'], $values['status']);
            flash('Registration added successfully.');
            redirect('registrations.php');
        } catch (PDOException $e) {
            $error = 'Unable to save the registration. Please try again.';
        }
    }
}

$pageTitle = 'New Registration';
$active = 'registrations';
require __DIR__ . '/includes/header.php';

page_head('New registration', 'Register a student for a school event.');
$submitLabel = 'Save registration';
require __DIR__ . '/includes/registration_form.php';
require __DIR__ . '/includes/footer.php';
