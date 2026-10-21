<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Registration.php';

$db = (new Database())->connect();
$regModel = new Registration($db);

$q = trim($_GET['q'] ?? '');
$registrations = $regModel->getAll($q);

$pageTitle = 'Registrations';
$active = 'registrations';
require __DIR__ . '/includes/header.php';

page_head(
    'Event registrations',
    'See which students are registered for school events.',
    '<a class="btn" href="add_registration.php">' . icon('plus') . 'New Registration</a>'
);
?>

<form class="toolbar" method="GET" action="registrations.php">
    <div class="search">
        <?= icon('search') ?>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search student, event or status">
    </div>
    <button class="btn secondary" type="submit">Search</button>
    <?php if ($q !== ''): ?><a class="btn ghost" href="registrations.php">Clear</a><?php endif; ?>
    <span class="count"><?= count($registrations) ?> registration<?= count($registrations) === 1 ? '' : 's' ?></span>
</form>

<section class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th><th>Event</th><th>Event date</th><th>Status</th><th>Attendance</th><th>Registered on</th><th class="right">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($registrations): foreach ($registrations as $r): ?>
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
                    <td><?= e($r['event_name']) ?></td>
                    <td class="num"><?= fmt_date($r['event_date']) ?></td>
                    <td><?= status_badge($r['status']) ?></td>
                    <td><?= $r['status'] === 'Cancelled' ? '<span class="person-sub">&mdash;</span>' : attendance_badge($r['attendance']) ?></td>
                    <td class="num"><?= fmt_date($r['created_at']) ?></td>
                    <td>
                        <div class="cell-actions">
                            <a class="btn secondary small" href="edit_registration.php?id=<?= (int) $r['registration_id'] ?>"><?= icon('edit') ?>Edit</a>
                            <a class="btn secondary small danger-soft" href="delete_registration.php?id=<?= (int) $r['registration_id'] ?>"><?= icon('trash') ?>Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="7"><div class="empty">
                    <strong><?= $q !== '' ? 'No matching registrations' : 'No registrations yet' ?></strong>
                    <?= $q !== '' ? 'Try a different search term.' : 'Register a student for an event to get started.' ?>
                </div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
