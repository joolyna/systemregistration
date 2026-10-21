<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Registration.php';

$db = (new Database())->connect();
$regModel = new Registration($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('Invalid registration ID.', 'error');
    redirect('registrations.php');
}

$stmt = $db->prepare(
    "SELECT r.*, s.student_name, e.event_name FROM registrations r
     INNER JOIN students s ON s.student_id = r.student_id
     INNER JOIN events e ON e.event_id = r.event_id
     WHERE r.registration_id = ?"
);
$stmt->execute([$id]);
$registration = $stmt->fetch();

if (!$registration) {
    flash('Registration not found.', 'error');
    redirect('registrations.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmId = filter_input(INPUT_POST, 'registration_id', FILTER_VALIDATE_INT);
    if ($confirmId !== $id) {
        flash('Invalid request.', 'error');
        redirect('registrations.php');
    }

    try {
        $regModel->delete($id);
        flash('Registration deleted.');
        redirect('registrations.php');
    } catch (PDOException $e) {
        $error = 'Unable to delete this registration. Please try again.';
    }
}

$pageTitle = 'Delete Registration';
$active = 'registrations';
require __DIR__ . '/includes/header.php';
?>
<div class="card confirm">
    <div class="confirm-icon"><?= icon('trash') ?></div>
    <h1>Delete this registration?</h1>
    <div class="target"><?= e($registration['student_name']) ?> &middot; <?= e($registration['event_name']) ?></div>
    <p>This action cannot be undone.</p>
    <?php if ($error !== ''): ?>
        <div class="alert alert-error alert-inline" style="margin-top:16px"><?= icon('alert') ?><span><?= e($error) ?></span></div>
    <?php endif; ?>
    <form method="POST" action="delete_registration.php?id=<?= (int) $id ?>">
        <input type="hidden" name="registration_id" value="<?= (int) $id ?>">
        <div class="form-actions">
            <button type="submit" class="btn danger">Yes, delete</button>
            <a class="btn ghost" href="registrations.php">Cancel</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
