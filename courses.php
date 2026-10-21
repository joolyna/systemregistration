<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Course.php';

$db = (new Database())->connect();
$courseModel = new Course($db);

$q = trim($_GET['q'] ?? '');
$courses = $courseModel->getAll($q);

// Group the courses under their college (rows already come sorted by college)
$groups = [];
foreach ($courses as $c) {
    $groups[$c['college']][] = $c;
}

$pageTitle = 'Courses';
$active = 'courses';
require __DIR__ . '/includes/header.php';

page_head(
    'Courses',
    'Courses are organized under their college. Students pick from these when they are added.',
    '<a class="btn" href="add_course.php">' . icon('plus') . 'Add Course</a>'
);
?>

<form class="toolbar" method="GET" action="courses.php">
    <div class="search">
        <?= icon('search') ?>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search course or college">
    </div>
    <button class="btn secondary" type="submit">Search</button>
    <?php if ($q !== ''): ?><a class="btn ghost" href="courses.php">Clear</a><?php endif; ?>
    <span class="count"><?= count($groups) ?> college<?= count($groups) === 1 ? '' : 's' ?> &middot; <?= count($courses) ?> course<?= count($courses) === 1 ? '' : 's' ?></span>
</form>

<?php if ($groups): foreach ($groups as $college => $items):
    $abbr = college_abbr($college);
    $totalStudents = array_sum(array_map(fn($c) => (int) $c['students'], $items));
?>
<section class="card college-card">
    <div class="college-head">
        <div class="college-title">
            <span class="abbr"><?= $abbr !== '' ? e($abbr) : icon('book') ?></span>
            <div>
                <h2><?= e(college_label($college)) ?></h2>
                <div class="person-sub">
                    <?= count($items) ?> course<?= count($items) === 1 ? '' : 's' ?> &middot;
                    <?= $totalStudents ?> student<?= $totalStudents === 1 ? '' : 's' ?>
                </div>
            </div>
        </div>
        <a class="btn secondary small" href="add_course.php?college=<?= urlencode($college) ?>"><?= icon('plus') ?>Add to <?= e($abbr !== '' ? $abbr : 'this college') ?></a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Course</th><th>Students</th><th class="right">Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($items as $c): ?>
                <tr>
                    <td class="person-name"><?= e($c['course_name']) ?></td>
                    <td class="num"><?= (int) $c['students'] ?></td>
                    <td>
                        <div class="cell-actions">
                            <a class="btn secondary small" href="edit_course.php?id=<?= (int) $c['course_id'] ?>"><?= icon('edit') ?>Edit</a>
                            <a class="btn secondary small danger-soft" href="delete_course.php?id=<?= (int) $c['course_id'] ?>"><?= icon('trash') ?>Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php endforeach; else: ?>
<section class="card">
    <div class="empty">
        <strong><?= $q !== '' ? 'No matching courses' : 'No courses yet' ?></strong>
        <?= $q !== '' ? 'Try a different search term.' : 'Add a course so it shows up in the student form.' ?>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
