<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Event.php';
require_once __DIR__ . '/classes/Registration.php';

$db = (new Database())->connect();
$eventModel = new Event($db);
$regModel = new Registration($db);

$eventId = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);

// ---------- Save attendance (POST) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $eventId) {
    $action = $_POST['action'] ?? 'set';

    if ($action === 'all_present') {
        $n = $regModel->markAllPresent($eventId);
        flash($n > 0 ? "$n student" . ($n === 1 ? '' : 's') . ' marked present.' : 'No pending students to mark.');
    } else {
        $regId = filter_input(INPUT_POST, 'registration_id', FILTER_VALIDATE_INT);
        $value = $_POST['attendance'] ?? '';
        if ($regId && in_array($value, ATTENDANCE, true)) {
            $regModel->setAttendance($regId, $eventId, $value);
            flash('Attendance saved.');
        } else {
            flash('Invalid attendance request.', 'error');
        }
    }
    redirect('attendance.php?event_id=' . $eventId);
}

$event = $eventId ? $eventModel->get($eventId) : false;
if ($eventId && !$event) {
    flash('Event not found.', 'error');
    redirect('attendance.php');
}

$pageTitle = 'Attendance';
$active = 'attendance';
require __DIR__ . '/includes/header.php';

// ---------- No event chosen: pick one ----------
if (!$event):
    $events = $eventModel->getAll();
    page_head('Attendance', 'Choose an event to mark who is present, absent or excused.');
?>
<?php if ($events): ?>
<div class="event-grid">
    <?php foreach ($events as $ev): ?>
        <a class="card event-pick" href="attendance.php?event_id=<?= (int) $ev['event_id'] ?>">
            <div class="person-name"><?= e($ev['event_name']) ?></div>
            <div class="person-sub"><?= fmt_date($ev['event_date']) ?><?= !empty($ev['venue']) ? ' &middot; ' . e($ev['venue']) : '' ?></div>
            <div class="mini-counts">
                <span class="badge badge-present"><?= (int) $ev['present'] ?> present</span>
                <span class="badge badge-absent"><?= (int) $ev['absent'] ?> absent</span>
                <span class="badge badge-excused"><?= (int) $ev['excused'] ?> excused</span>
            </div>
            <div class="link-arrow">Take attendance <?= icon('arrow') ?></div>
        </a>
    <?php endforeach; ?>
</div>
<?php else: ?>
<section class="card"><div class="empty"><strong>No events yet</strong>Add an event first, then register students.</div></section>
<?php endif; ?>
<?php
    require __DIR__ . '/includes/footer.php';
    exit;
endif;

// ---------- Attendance sheet for one event ----------
$rows = $regModel->getByEvent($eventId);
$counts = ['Pending' => 0, 'Present' => 0, 'Absent' => 0, 'Excused' => 0];
foreach ($rows as $r) { $counts[$r['attendance']]++; }

page_head(
    $event['event_name'],
    fmt_date($event['event_date']) . (!empty($event['venue']) ? ' · ' . $event['venue'] : ''),
    '<a class="btn ghost" href="attendance.php">All events</a>'
  . ($counts['Pending'] > 0
        ? '<form method="POST" action="attendance.php?event_id=' . (int) $eventId . '" style="display:inline">'
        . '<input type="hidden" name="action" value="all_present">'
        . '<button class="btn" type="submit">' . icon('check') . 'Mark all pending as present</button></form>'
        : '')
);
?>

<section class="stats">
    <div class="card stat"><div><div class="stat-label">Pending</div><div class="stat-value"><?= $counts['Pending'] ?></div></div></div>
    <div class="card stat"><div><div class="stat-label">Present</div><div class="stat-value" style="color:var(--success)"><?= $counts['Present'] ?></div></div></div>
    <div class="card stat"><div><div class="stat-label">Absent</div><div class="stat-value" style="color:var(--danger)"><?= $counts['Absent'] ?></div></div></div>
    <div class="card stat"><div><div class="stat-label">Excused</div><div class="stat-value" style="color:var(--warn)"><?= $counts['Excused'] ?></div></div></div>
</section>

<section class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Student</th><th>Year level</th><th>Attendance</th><th class="right">Mark as</th></tr>
            </thead>
            <tbody>
            <?php if ($rows): foreach ($rows as $r): ?>
                <tr>
                    <td>
                        <div class="person">
                            <div class="avatar"><?= initials($r['student_name']) ?></div>
                            <div>
                                <div class="person-name"><?= e($r['student_name']) ?></div>
                                <div class="person-sub"><?= e($r['course']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= e($r['year_level']) ?></td>
                    <td><?= attendance_badge($r['attendance']) ?></td>
                    <td>
                        <form class="seg" method="POST" action="attendance.php?event_id=<?= (int) $eventId ?>">
                            <input type="hidden" name="registration_id" value="<?= (int) $r['registration_id'] ?>">
                            <?php foreach (['Present', 'Absent', 'Excused'] as $opt): ?>
                                <button type="submit" name="attendance" value="<?= $opt ?>"
                                        class="seg-btn seg-<?= strtolower($opt) ?> <?= $r['attendance'] === $opt ? 'on' : '' ?>"><?= $opt ?></button>
                            <?php endforeach; ?>
                            <?php if ($r['attendance'] !== 'Pending'): ?>
                                <button type="submit" name="attendance" value="Pending" class="seg-btn seg-reset" title="Reset to pending">Reset</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="4"><div class="empty">
                    <strong>No registered students</strong>
                    Register students for this event first. Cancelled registrations are not listed here.
                </div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
