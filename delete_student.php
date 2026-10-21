<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Student.php';

$db = (new Database())->connect();
$studentModel = new Student($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('Invalid student ID.', 'error');
    redirect('students.php');
}

$student = $studentModel->get($id);
if (!$student) {
    flash('Student record not found.', 'error');
    redirect('students.php');
}

$regStmt = $db->prepare("SELECT COUNT(*) FROM registrations WHERE student_id = ?");
$regStmt->execute([$id]);
$linked = (int) $regStmt->fetchColumn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmId = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    if ($confirmId !== $id) {
        flash('Invalid request.', 'error');
        redirect('students.php');
    }

    try {
        $studentModel->delete($id);
        flash('Student deleted.');
        redirect('students.php');
    } catch (PDOException $e) {
        $error = 'Unable to delete this student. Please try again.';
    }
}

$pageTitle = 'Delete Student';
$active = 'students';
require __DIR__ . '/includes/header.php';
?>
<div class="card confirm">
    <div class="confirm-icon"><?= icon('trash') ?></div>
    <h1>Delete this student?</h1>
    <div class="target"><?= e($student['student_name']) ?></div>
    <p>This action cannot be undone.</p>
    <?php if ($linked > 0): ?>
        <p>Their <?= $linked ?> event registration<?= $linked === 1 ? '' : 's' ?> will also be deleted.</p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <div class="alert alert-error alert-inline" style="margin-top:16px"><?= icon('alert') ?><span><?= e($error) ?></span></div>
    <?php endif; ?>
    <form method="POST" action="delete_student.php?id=<?= (int) $id ?>">
        <input type="hidden" name="student_id" value="<?= (int) $id ?>">
        <div class="form-actions">
            <button type="submit" class="btn danger">Yes, delete</button>
            <a class="btn ghost" href="students.php">Cancel</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
