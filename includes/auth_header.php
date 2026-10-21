<?php
/* expects: $pageTitle */
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
<body class="auth-body">
<div class="auth">
    <aside class="auth-side">
        <a class="brand" href="login.php">
            <span class="brand-mark"><?= icon('calendar') ?></span>
            <span class="brand-text">EventReg<small>Student portal</small></span>
        </a>
        <div class="auth-pitch">
            <h2>Run campus events without the paperwork.</h2>
            <p>Manage students, seats, registrations and attendance in one place.</p>
        </div>
        <div class="auth-foot">Student Event Registration System</div>
    </aside>
    <main class="auth-main">
        <div class="auth-card">
