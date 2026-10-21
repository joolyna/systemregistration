<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Student Event Registration';
$active    = $active ?? '';
$nav = [
    'dashboard'     => ['index.php',         'Dashboard',     'home'],
    'students'      => ['students.php',      'Students',      'users'],
    'courses'       => ['courses.php',       'Courses',       'book'],
    'events'        => ['events.php',        'Events',        'calendar'],
    'registrations' => ['registrations.php', 'Registrations', 'ticket'],
    'attendance'    => ['attendance.php',    'Attendance',    'check'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Student Event Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <a class="brand" href="index.php">
            <span class="brand-mark"><?= icon('calendar') ?></span>
            <span class="brand-text">EventReg<small>Student portal</small></span>
        </a>
        <nav class="nav">
            <?php foreach ($nav as $key => [$href, $label, $ico]): ?>
                <a class="nav-link <?= $active === $key ? 'active' : '' ?>" href="<?= $href ?>">
                    <?= icon($ico) ?><span><?= $label ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <?php $me = current_user(); ?>
        <form class="user-chip" method="POST" action="logout.php">
            <?= csrf_field() ?>
            <div class="avatar"><?= initials($me['name']) ?></div>
            <div class="user-info">
                <div class="user-name"><?= e($me['name']) ?></div>
                <div class="user-mail"><?= e($me['email']) ?></div>
            </div>
            <button type="submit" class="logout-btn" title="Log out" aria-label="Log out"><?= icon('logout') ?></button>
        </form>
        <div class="sidebar-foot">Student Event Registration System</div>
    </aside>

    <main class="main">
        <?php if (!empty($_SESSION['flash'])): $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
            <div class="alert alert-<?= e($f['type']) ?>" role="status">
                <?= icon($f['type'] === 'success' ? 'check' : 'alert') ?>
                <span><?= e($f['message']) ?></span>
            </div>
        <?php endif; ?>
