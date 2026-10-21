<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Student.php';

$db = (new Database())->connect();
$studentModel = new Student($db);

$error = '';
$values = ['student_name' => '', 'course' => '', 'year_level' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $_) {
        $values[$key] = trim($_POST[$key] ?? '');
    }

    if (in_array('', $values, true)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!in_array($values['course'], all_courses($db), true)) {
        $error = 'Please choose a course from the list.';
    } elseif (!in_array($values['year_level'], YEAR_LEVELS, true)) {
        $error = 'Please choose a valid year level.';
    } else {
        try {
            $studentModel->add($values['student_name'], $values['course'], $values['year_level'], $values['email']);
            flash('Student added successfully.');
            redirect('students.php');
        } catch (PDOException $e) {
            $error = 'Unable to save the student. Please try again.';
        }
    }
}

$pageTitle = 'Add Student';
$active = 'students';
require __DIR__ . '/includes/header.php';

page_head('Add student', 'Enter the student\'s information below.');
$submitLabel = 'Save student';
require __DIR__ . '/includes/student_form.php';
require __DIR__ . '/includes/footer.php';
