<?php
define('PUBLIC_PAGE', true);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';

if (is_logged_in()) {
    redirect('index.php');
}

$db    = (new Database())->connect();
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_valid()) {
        $error = 'Your session expired. Please try again.';
    } elseif ($email === '' || $password === '') {
        $error = 'Enter your email and password.';
    } else {
        try {
            $stmt = $db->prepare("SELECT user_id, full_name, email, password_hash FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Always run password_verify so response time doesn't reveal whether the email exists.
            $hash = $user['password_hash'] ?? '$2y$10$usesomesillystringforsaltandhashingxxxxxxxxxxxxxxxxxxxxxx';
            if (password_verify($password, $hash) && $user) {
                if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                    $db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?")
                       ->execute([password_hash($password, PASSWORD_DEFAULT), $user['user_id']]);
                }
                login_user($user);
                flash('Welcome back, ' . $user['full_name'] . '!');
                redirect('index.php');
            }
            $error = 'Incorrect email or password.';
        } catch (PDOException $e) {
            $error = 'The users table is missing. Import migrate_users.sql in phpMyAdmin first.';
        }
    }
}

$pageTitle = 'Log in';
require __DIR__ . '/includes/auth_header.php';
?>
<h1>Welcome back</h1>
<p class="subtitle">Log in to manage your events.</p>

<?php if (!empty($_SESSION['flash'])): $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
    <div class="alert alert-<?= e($f['type']) ?> alert-inline"><?= icon($f['type'] === 'success' ? 'check' : 'alert') ?><span><?= e($f['message']) ?></span></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error alert-inline"><?= icon('alert') ?><span><?= e($error) ?></span></div>
<?php endif; ?>

<form method="POST" action="login.php" novalidate>
    <?= csrf_field() ?>
    <div class="field">
        <label for="email">Email</label>
        <div class="input-icon"><?= icon('mail') ?>
            <input type="email" id="email" name="email" placeholder="you@example.com" value="<?= e($email) ?>" autocomplete="email" required autofocus>
        </div>
    </div>
    <div class="field">
        <label for="password">Password</label>
        <div class="input-icon"><?= icon('lock') ?>
            <input type="password" id="password" name="password" placeholder="Your password" autocomplete="current-password" required>
            <button type="button" class="pw-toggle" data-toggle-pw="password">Show</button>
        </div>
    </div>
    <button type="submit" class="btn auth-submit">Log in</button>
</form>
<p class="auth-switch">New here? <a href="register.php">Create an account</a></p>
<?php require __DIR__ . '/includes/auth_footer.php'; ?>
