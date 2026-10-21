<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Event.php';

$db = (new Database())->connect();
$eventModel = new Event($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('Invalid event ID.', 'error');
    redirect('events.php');
}

$event = $eventModel->get($id);
if (!$event) {
    flash('Event not found.', 'error');
    redirect('events.php');
}

$cnt = $db->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
$cnt->execute([$id]);
$linked = (int) $cnt->fetchColumn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
    if ($confirmId !== $id) {
        flash('Invalid request.', 'error');
        redirect('events.php');
    }

    try {
        $eventModel->delete($id);
        flash('Event deleted.');
        redirect('events.php');
    } catch (PDOException $e) {
        $error = 'Unable to delete this event. Please try again.';
    }
}

$pageTitle = 'Delete Event';
$active = 'events';
require __DIR__ . '/includes/header.php';
?>
<div class="card confirm">
    <div class="confirm-icon"><?= icon('trash') ?></div>
    <h1>Delete this event?</h1>
    <div class="target"><?= e($event['event_name']) ?> &middot; <?= fmt_date($event['event_date']) ?></div>
    <p>This action cannot be undone.</p>
    <?php if ($linked > 0): ?>
        <p>Its <?= $linked ?> registration<?= $linked === 1 ? '' : 's' ?> will also be deleted.</p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <div class="alert alert-error alert-inline" style="margin-top:16px"><?= icon('alert') ?><span><?= e($error) ?></span></div>
    <?php endif; ?>
    <form method="POST" action="delete_event.php?id=<?= (int) $id ?>">
        <input type="hidden" name="event_id" value="<?= (int) $id ?>">
        <div class="form-actions">
            <button type="submit" class="btn danger">Yes, delete</button>
            <a class="btn ghost" href="events.php">Cancel</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
