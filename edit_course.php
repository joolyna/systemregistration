<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/course_validate.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Course.php';

$db = (new Database())->connect();
$courseModel = new Course($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('Invalid course. Please use the Edit button in the courses table.', 'error');
    redirect('courses.php');
}

$course = $courseModel->get($id);
if (!$course) {
    flash('Course not found.', 'error');
    redirect('courses.php');
}

$error = '';
$values = ['college' => $course['college'], 'course_name' => $course['course_name']];
$enrolled = $courseModel->studentCount($course['course_name']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$values, $error] = read_course_post();

    if ($error === '' && $courseModel->nameTaken($values['course_name'], $id)) {
        $error = 'Another course already has that name.';
    }

    if ($error === '') {
        try {
            $courseModel->update($id, $values['college'], $values['course_name']);
            flash('Course updated successfully.');
            redirect('courses.php');
        } catch (PDOException $e) {
            $error = 'Unable to update the course. Please try again.';
        }
    }
}

$colleges = $courseModel->colleges();
$pageTitle = 'Edit Course';
$active = 'courses';
require __DIR__ . '/includes/header.php';

$sub = $enrolled > 0
    ? 'Renaming also updates the ' . $enrolled . ' student' . ($enrolled === 1 ? '' : 's') . ' in this course.'
    : 'Update the course details below.';
page_head('Edit course', $sub);
$submitLabel = 'Update course';
require __DIR__ . '/includes/course_form.php';
require __DIR__ . '/includes/footer.php';
