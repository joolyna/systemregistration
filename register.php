<?php
define('PUBLIC_PAGE', true);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/connection.php';

if (is_logged_in()) {
    redirect('index.php');
}

$db     = (new Database())->connect();
$error  = '';
$values = ['full_name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['full_name'] = trim($_POST['full_name'] ?? '');
    $values['email']     = strtolower(trim($_POST['email'] ?? ''));
    $password            = (string) ($_POST['password'] ?? '');
    $confirm             = (string) ($_POST['confirm_password'] ?? '');

    if (!csrf_valid()) {
        $error = 'Your session expired. Please try again.';
    } elseif (mb_strlen($values['full_name']) < 2 || mb_strlen($values['full_name']) > 120) {
        $error = 'Enter your full name (2 to 120 characters).';
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($values['email']) > 150) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'The passwords do not match.';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$values['full_name'], $values['email'], password_hash($password, PASSWORD_DEFAULT)]);

            login_user([
                'user_id'   => $db->lastInsertId(),
                'full_name' => $values['full_name'],
                'email'     => $values['email'],
            ]);
            flash('Account created. Welcome, ' . $values['full_name'] . '!');
            redirect('index.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'An account with that email already exists.';
            } else {
                $error = 'Could not create the account. Did you import migrate_users.sql?';
            }
        }
    }
}

$pageTitle = 'Create account';
require __DIR__ . '/includes/auth_header.php';
?>
<h1>Create your account</h1>
<p class="subtitle">It only takes a minute.</p>

<?php if ($error): ?>
    <div class="alert alert-error alert-inline"><?= icon('alert') ?><span><?= e($error) ?></span></div>
<?php endif; ?>

<form method="POST" action="register.php" novalidate>
    <?= csrf_field() ?>
    <div class="field">
        <label for="full_name">Full name</label>
        <div class="input-icon"><?= icon('user') ?>
            <input type="text" id="full_name" name="full_name" maxlength="120" placeholder="Juan Dela Cruz" value="<?= e($values['full_name']) ?>" autocomplete="name" required autofocus>
        </div>
    </div>
    <div class="field">
        <label for="email">Email</label>
        <div class="input-icon"><?= icon('mail') ?>
            <input type="email" id="email" name="email" maxlength="150" placeholder="you@example.com" value="<?= e($values['email']) ?>" autocomplete="email" required>
        </div>
    </div>
    <div class="field">
        <label for="password">Password</label>
        <div class="input-icon"><?= icon('lock') ?>
            <input type="password" id="password" name="password" minlength="8" placeholder="At least 8 characters" autocomplete="new-password" required>
            <button type="button" class="pw-toggle" data-toggle-pw="password">Show</button>
        </div>
    </div>
    <div class="field">
        <label for="confirm_password">Confirm password</label>
        <div class="input-icon"><?= icon('lock') ?>
            <input type="password" id="confirm_password" name="confirm_password" minlength="8" placeholder="Repeat your password" autocomplete="new-password" required>
            <button type="button" class="pw-toggle" data-toggle-pw="confirm_password">Show</button>
        </div>
    </div>
    <button type="submit" class="btn auth-submit">Create account</button>
</form>
<p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
<?php require __DIR__ . '/includes/auth_footer.php'; ?>
