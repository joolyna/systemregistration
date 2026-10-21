<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Student.php';

$db = (new Database())->connect();
$studentModel = new Student($db);

$q = trim($_GET['q'] ?? '');
$students = $studentModel->getAll($q);

$pageTitle = 'Students';
$active = 'students';
require __DIR__ . '/includes/header.php';

page_head(
    'Students',
    'Manage the students who can register for school events.',
    '<a class="btn" href="add_student.php">' . icon('plus') . 'Add Student</a>'
);
?>

<form class="toolbar" method="GET" action="students.php">
    <div class="search">
        <?= icon('search') ?>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search name, course, year or email">
    </div>
    <button class="btn secondary" type="submit">Search</button>
    <?php if ($q !== ''): ?><a class="btn ghost" href="students.php">Clear</a><?php endif; ?>
    <span class="count"><?= count($students) ?> student<?= count($students) === 1 ? '' : 's' ?></span>
</form>

<section class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th><th>Course</th><th>Year level</th><th>Joined</th><th class="right">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($students): foreach ($students as $s): ?>
                <tr>
                    <td>
                        <div class="person">
                            <div class="avatar"><?= initials($s['student_name']) ?></div>
                            <div>
                                <div class="person-name"><?= e($s['student_name']) ?></div>
                                <div class="person-sub"><?= e($s['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="pill"><?= e($s['course']) ?></span></td>
                    <td><?= e($s['year_level']) ?></td>
                    <td class="num"><?= fmt_date($s['created_at']) ?></td>
                    <td>
                        <div class="cell-actions">
                            <a class="btn secondary small" href="edit_student.php?id=<?= (int) $s['student_id'] ?>"><?= icon('edit') ?>Edit</a>
                            <a class="btn secondary small danger-soft" href="delete_student.php?id=<?= (int) $s['student_id'] ?>"><?= icon('trash') ?>Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="5"><div class="empty">
                    <strong><?= $q !== '' ? 'No matching students' : 'No students yet' ?></strong>
                    <?= $q !== '' ? 'Try a different search term.' : 'Add your first student to get started.' ?>
                </div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
