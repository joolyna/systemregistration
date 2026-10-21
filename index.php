<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';

$db = (new Database())->connect();

$students  = (int) $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
$regs      = (int) $db->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
$attended  = (int) $db->query("SELECT COUNT(*) FROM registrations WHERE attendance = 'Present'")->fetchColumn();
$events    = (int) $db->query("SELECT COUNT(*) FROM events")->fetchColumn();

$latest = $db->query(
    "SELECT r.registration_id, s.student_name, s.course, e.event_name, e.event_date, r.status
     FROM registrations r
     INNER JOIN students s ON s.student_id = r.student_id
     INNER JOIN events e ON e.event_id = r.event_id
     ORDER BY r.registration_id DESC
     LIMIT 5"
)->fetchAll();

$pageTitle = 'Dashboard';
$active = 'dashboard';
require __DIR__ . '/includes/header.php';

page_head(
    'Dashboard',
    'Welcome back! Here is what is happening with your events today.',
    '<a class="btn secondary" href="add_student.php">' . icon('plus') . 'Add Student</a>'
  . '<a class="btn" href="add_registration.php">' . icon('plus') . 'New Registration</a>'
);
?>

<section class="stats">
    <div class="card stat">
        <div class="stat-icon"><?= icon('users') ?></div>
        <div><div class="stat-label">Total Students</div><div class="stat-value"><?= $students ?></div></div>
    </div>
    <div class="card stat">
        <div class="stat-icon"><?= icon('calendar') ?></div>
        <div><div class="stat-label">Registrations</div><div class="stat-value"><?= $regs ?></div></div>
    </div>
    <div class="card stat">
        <div class="stat-icon"><?= icon('check') ?></div>
        <div><div class="stat-label">Marked Present</div><div class="stat-value"><?= $attended ?></div></div>
    </div>
    <div class="card stat">
        <div class="stat-icon"><?= icon('home') ?></div>
        <div><div class="stat-label">Events</div><div class="stat-value"><?= $events ?></div></div>
    </div>
</section>

<section class="card">
    <div class="panel" style="padding-bottom:8px">
        <div class="section-head">
            <h2>Latest registrations</h2>
            <a class="link-arrow" href="registrations.php">View all <?= icon('arrow') ?></a>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Student</th><th>Event</th><th>Event date</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if ($latest): foreach ($latest as $row): ?>
                <tr>
                    <td>
                        <div class="person">
                            <div class="avatar"><?= initials($row['student_name']) ?></div>
                            <div>
                                <div class="person-name"><?= e($row['student_name']) ?></div>
                                <div class="person-sub"><?= e($row['course']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= e($row['event_name']) ?></td>
                    <td class="num"><?= fmt_date($row['event_date']) ?></td>
                    <td><?= status_badge($row['status']) ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="4"><div class="empty"><strong>No registrations yet</strong>Add a registration to see it here.</div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
