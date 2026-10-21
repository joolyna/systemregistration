<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/registration_validate.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Registration.php';
require_once __DIR__ . '/classes/Event.php';

$db = (new Database())->connect();
$regModel = new Registration($db);
$eventModel = new Event($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('Invalid registration. Please use the Edit button in the registrations table.', 'error');
    redirect('registrations.php');
}

$registration = $regModel->get($id);
if (!$registration) {
    flash('Registration not found.', 'error');
    redirect('registrations.php');
}

$students = $db->query("SELECT student_id, student_name, course FROM students ORDER BY student_name ASC")->fetchAll();

$events = $eventModel->getAll();

$error = '';
$values = [
    'student_id' => $registration['student_id'],
    'event_id'   => $registration['event_id'],
    'status'     => $registration['status'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$values, $error] = read_registration_post($db, $id);

    if ($error === '') {
        try {
            $regModel->update($id, (int) $values['student_id'], (int) $values['event_id'], $values['status']);
            flash('Registration updated successfully.');
            redirect('registrations.php');
        } catch (PDOException $e) {
            $error = 'Unable to update the registration. Please try again.';
        }
    }
}

$pageTitle = 'Edit Registration';
$active = 'registrations';
require __DIR__ . '/includes/header.php';

page_head('Edit registration', 'Update the registration details below.');
$submitLabel = 'Update registration';
require __DIR__ . '/includes/registration_form.php';
require __DIR__ . '/includes/footer.php';
