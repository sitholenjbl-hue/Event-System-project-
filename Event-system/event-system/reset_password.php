<?php
session_start();
require 'db.php';

$errors = [];
$token  = $_GET['token'] ?? $_POST['token'] ?? '';
$valid  = false;

if ($token !== '') {
    $stmt = $pdo->prepare(
        'SELECT user_id FROM password_resets WHERE token_hash = ? AND expires_at > ?'
    );
    $stmt->execute([hash('sha256', $token), date('Y-m-d H:i:s')]);
    $reset = $stmt->fetch();
    $valid = (bool) $reset;
}

if ($valid && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if (strlen($password) < 10) {
        $errors[] = 'Your password must be at least 10 characters long.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
            ->execute([$hash, $reset['user_id']]);

        // token can only be used once
        $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')
            ->execute([$reset['user_id']]);

        header('Location: login.php');
        exit;
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Online Event Management System - Reset Password</title>
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <h1>Online Event Management System</h1>

    <div class="container">
      <div class="form-header">
        <h2>Reset your password</h2>
      </div>

      <?php if ($valid): ?>

        <form class="form-box" method="post" action="reset_password.php">

          <?php if (!empty($errors)): ?>
            <ul class="errors">
              <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />

          <div class="input-group">
            <input
              type="password"
              id="password"
              name="password"
              class="input-field"
              placeholder=""
              minlength="10"
              required
            />
            <label for="password" class="floating-label">New password</label>
          </div>

          <button type="submit" class="form-btn form-btn--submit">Change password</button>
        </form>

      <?php else: ?>

        <div class="form-bottom">
          <p>This reset link is invalid or has expired.</p>
          <p><a href="forgot_password.php" class="form-link">Request a new link</a></p>
        </div>

      <?php endif; ?>
    </div>
  </body>
</html>