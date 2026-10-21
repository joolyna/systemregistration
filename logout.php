<?php
require_once __DIR__ . '/includes/functions.php';

// Logout is POST-only (with a CSRF token) so other sites can't log you out via a link or image.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    logout_user();
    session_start();
    flash('You have been logged out.');
}
redirect('login.php');
