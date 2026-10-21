<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Student.php';

$db = (new Database())->connect();
$studentModel = new Student($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('Invalid student. Please use the Edit button in the students table.', 'error');
    redirect('students.php');
}

$student = $studentModel->get($id);
if (!$student) {
    flash('Student record not found.', 'error');
    redirect('students.php');
}

$error = '';
$values = [
    'student_name' => $student['student_name'],
    'course'       => $student['course'],
    'year_level'   => $student['year_level'],
    'email'        => (string) $student['email'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $_) {
        $values[$key] = trim($_POST[$key] ?? '');
    }

    if (in_array('', $values, true)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!in_array($values['course'], all_courses($db), true) && $values['course'] !== $student['course']) {
        $error = 'Please choose a course from the list.';
    } elseif (!in_array($values['year_level'], YEAR_LEVELS, true)) {
        $error = 'Please choose a valid year level.';
    } else {
        try {
            $studentModel->update($id, $values['student_name'], $values['course'], $values['year_level'], $values['email']);
            flash('Student updated successfully.');
            redirect('students.php');
        } catch (PDOException $e) {
            $error = 'Unable to update the student. Please try again.';
        }
    }
}

$pageTitle = 'Edit Student';
$active = 'students';
require __DIR__ . '/includes/header.php';

page_head('Edit student', 'Update the student\'s information below.');
$submitLabel = 'Update student';
require __DIR__ . '/includes/student_form.php';
require __DIR__ . '/includes/footer.php';
