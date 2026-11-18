<?php
session_start();
require 'db.php';

$message = '';
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
    
            $token = bin2hex(random_bytes(32));

            $token_hash = hash('sha256', $token);
            $expires    = date('Y-m-d H:i:s', time() + 3600);   // valid for 1 hour

    
            $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')
                ->execute([$user['id']]);

            $pdo->prepare(
                'INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
            )->execute([$user['id'], $token_hash, $expires]);

        
            $link = 'reset_password.php?token=' . $token;
            $message = 'A reset link has been generated (simulated email): '
                     . '<a href="' . htmlspecialchars($link) . '">Reset your password</a>';
        } else {
            $message = 'If that email is registered, a reset link has been sent.';
        }
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Online Event Management System - Forgot Password</title>
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <h1>Online Event Management System</h1>

    <div class="container">
      <div class="form-container">
        <div class="form blob">
          <img src="images/blob.svg" alt="" class="blob-image blob-image--1" />
          <img src="images/blob.svg" alt="" class="blob-image blob-image--2" />
          <img src="images/blob.svg" alt="" class="blob-image blob-image--3" />
        </div>
      </div>

      <div class="form-header">
        <p>Enter your email you registered with</p>
        <h2>Forgot your password?</h2>
      </div>

      <form class="form-box" method="post" action="forgot_password.php">

        <?php if (!empty($errors)): ?>
          <ul class="errors">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <?php if ($message !== ''): ?>
          <p class="message"><?= $message ?></p>
        <?php endif; ?>

        <div class="input-group">
          <input
            type="email"
            id="email"
            name="email"
            class="input-field"
            placeholder=""
            maxlength="150"
            required
          />
          <label for="email" class="floating-label">Email address</label>
        </div>

        <button type="submit" class="form-btn form-btn--submit">Send reset link</button>
      </form>

      <div class="form-bottom">
        <p>Remembered it? <a href="login.php" class="form-link">Back to log in</a></p>
      </div>
    </div>
  </body>
</html>