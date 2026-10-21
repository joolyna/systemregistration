<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Event.php';

$db = (new Database())->connect();
$eventModel = new Event($db);

$q = trim($_GET['q'] ?? '');
$events = $eventModel->getAll($q);

$pageTitle = 'Events';
$active = 'events';
require __DIR__ . '/includes/header.php';

page_head(
    'Events',
    'Create school events and set how many students can join.',
    '<a class="btn" href="add_event.php">' . icon('plus') . 'Add Event</a>'
);
?>

<form class="toolbar" method="GET" action="events.php">
    <div class="search">
        <?= icon('search') ?>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search event name or venue">
    </div>
    <button class="btn secondary" type="submit">Search</button>
    <?php if ($q !== ''): ?><a class="btn ghost" href="events.php">Clear</a><?php endif; ?>
    <span class="count"><?= count($events) ?> event<?= count($events) === 1 ? '' : 's' ?></span>
</form>

<section class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Event</th><th>Date</th><th>Seats</th><th>Availability</th><th class="right">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($events): foreach ($events as $ev):
                $cap = max(1, (int) $ev['capacity']);
                $taken = (int) $ev['taken'];
                $isFull = $taken >= $cap;
                $pct = min(100, (int) round($taken / $cap * 100));
            ?>
                <tr>
                    <td>
                        <div class="person-name"><?= e($ev['event_name']) ?></div>
                        <?php if (!empty($ev['venue'])): ?>
                            <div class="venue"><?= icon('pin') ?><?= e($ev['venue']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="num"><?= fmt_date($ev['event_date']) ?></td>
                    <td>
                        <div class="meter">
                            <div class="meter-track"><div class="meter-fill <?= $isFull ? 'full' : '' ?>" style="width:<?= $pct ?>%"></div></div>
                            <span class="meter-text"><?= $taken ?>/<?= $cap ?></span>
                        </div>
                    </td>
                    <td><?= $isFull ? '<span class="badge badge-full">Full</span>' : '<span class="badge badge-open">' . ($cap - $taken) . ' left</span>' ?></td>
                    <td>
                        <div class="cell-actions">
                            <a class="btn secondary small" href="attendance.php?event_id=<?= (int) $ev['event_id'] ?>"><?= icon('check') ?>Attendance</a>
                            <a class="btn secondary small" href="edit_event.php?id=<?= (int) $ev['event_id'] ?>"><?= icon('edit') ?>Edit</a>
                            <a class="btn secondary small danger-soft" href="delete_event.php?id=<?= (int) $ev['event_id'] ?>"><?= icon('trash') ?>Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="5"><div class="empty">
                    <strong><?= $q !== '' ? 'No matching events' : 'No events yet' ?></strong>
                    <?= $q !== '' ? 'Try a different search term.' : 'Add your first event so students can register.' ?>
                </div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
