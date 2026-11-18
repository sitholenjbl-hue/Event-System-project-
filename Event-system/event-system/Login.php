<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';


$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');

    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, full_name, password_hash FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);          // prevents session fixation
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $errors[] = 'Invalid email or password!!.';
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Online Event Management System - Log in</title>
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
        <p>Please enter your details</p>
        <h2>Welcome back</h2>
      </div>

      <form class="form-box" method="post" action="login.php">

        <?php if (!empty($errors)): ?>
          <ul class="errors">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
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
            value="<?= htmlspecialchars($email ?? '') ?>"
          />
          <label for="email" class="floating-label">Email address</label>
        </div>

        <div class="input-group">
          <input
            type="password"
            id="password"
            name="password"
            class="input-field"
            placeholder=""
            required
          />
          <label for="password" class="floating-label">Password</label>
        </div>

        <div class="form-col">
          <a href="forgot_password.php" class="form-link">Forgot password?</a>
        </div>

        <button type="submit" class="form-btn form-btn--submit">Log in</button>
      </form>

      <div class="form-bottom">
        <p>Don't have an account? <a href="register.php" class="form-link">Register</a></p>
      </div>
    </div>
  </body>
</html>