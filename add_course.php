<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/course_validate.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Course.php';

$db = (new Database())->connect();
$courseModel = new Course($db);

$error = '';
$values = ['college' => trim($_GET['college'] ?? ''), 'course_name' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$values, $error] = read_course_post();

    if ($error === '' && $courseModel->nameTaken($values['course_name'])) {
        $error = 'That course already exists.';
    }

    if ($error === '') {
        try {
            $courseModel->add($values['college'], $values['course_name']);
            flash('Course added successfully.');
            redirect('courses.php');
        } catch (PDOException $e) {
            $error = 'Unable to save the course. Please try again.';
        }
    }
}

$colleges = $courseModel->colleges();
$pageTitle = 'Add Course';
$active = 'courses';
require __DIR__ . '/includes/header.php';

page_head('Add course', 'It will appear in the Course dropdown of the student form.');
$submitLabel = 'Save course';
require __DIR__ . '/includes/course_form.php';
require __DIR__ . '/includes/footer.php';
