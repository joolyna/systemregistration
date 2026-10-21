<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Course.php';

$db = (new Database())->connect();
$courseModel = new Course($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('Invalid course ID.', 'error');
    redirect('courses.php');
}

$course = $courseModel->get($id);
if (!$course) {
    flash('Course not found.', 'error');
    redirect('courses.php');
}

$linked = $courseModel->studentCount($course['course_name']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmId = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
    if ($confirmId !== $id) {
        flash('Invalid request.', 'error');
        redirect('courses.php');
    }

    if ($linked > 0) {
        $error = 'This course still has students. Move them to another course first.';
    } else {
        try {
            $courseModel->delete($id);
            flash('Course deleted.');
            redirect('courses.php');
        } catch (PDOException $e) {
            $error = 'Unable to delete this course. Please try again.';
        }
    }
}

$pageTitle = 'Delete Course';
$active = 'courses';
require __DIR__ . '/includes/header.php';
?>
<div class="card confirm">
    <div class="confirm-icon"><?= icon('trash') ?></div>
    <h1>Delete this course?</h1>
    <div class="target"><?= e($course['course_name']) ?></div>
    <?php if ($linked > 0): ?>
        <p>It can't be deleted yet: <?= $linked ?> student<?= $linked === 1 ? ' is' : 's are' ?> still in this course.
           Edit those students to another course first.</p>
    <?php else: ?>
        <p>This action cannot be undone.</p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <div class="alert alert-error alert-inline" style="margin-top:16px"><?= icon('alert') ?><span><?= e($error) ?></span></div>
    <?php endif; ?>
    <form method="POST" action="delete_course.php?id=<?= (int) $id ?>">
        <input type="hidden" name="course_id" value="<?= (int) $id ?>">
        <div class="form-actions">
            <?php if ($linked === 0): ?><button type="submit" class="btn danger">Yes, delete</button><?php endif; ?>
            <a class="btn ghost" href="courses.php"><?= $linked > 0 ? 'Back to courses' : 'Cancel' ?></a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
