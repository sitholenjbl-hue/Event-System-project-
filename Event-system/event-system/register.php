<?php

session_start();
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';


    if (empty($full_name)) {
        $errors[] = "Please enter your name!!";
    } 
    elseif (strlen($full_name) > 100) {
        $errors[] = "Your name must not exceed 100 characters!!";
    }

    if (empty($email)) {
        $errors[] = "Please enter your email, it is required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }

    if (empty($password)) {
        $errors[] = "Please enter your password!";
    } 
    elseif (strlen($password) < 10) {
        $errors[] = "Your password must be at within 10 characters long!!";
    }
    $profile_image = null; 

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['profile_image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "The picture could not be uploaded.";
    } elseif ($file['size'] > 2 * 1024 * 1024) {    
        $errors[] = "The picture must be smaller than 2MB.";
    } else {
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowed[$mime])) {
            $errors[] = "Only JPG, PNG or WEBP pictures are allowed.";
        } else {
            // random new name, never trust the original filename
            $profile_image = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
        }
    }
}

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'That email is already registered.';
        }
    }
// error check
 if (empty($errors)) {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    if ($profile_image !== null) {
        move_uploaded_file($_FILES['profile_image']['tmp_name'], 'uploads/' . $profile_image);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO users (full_name, email, password_hash, profile_image) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$full_name, $email, $hash, $profile_image]);

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
    <title>Online Event Management System - Register</title>
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
        <h2>Create your account</h2>
      </div>

      <form class="form-box" method="post" action="register.php" enctype="multipart/form-data">
<form id="register-form" class="form-box" method="post" action="register.php" enctype="multipart/form-data">  <!-- 1. added id="register-form" -->

  <p id="js-error" class="errors"></p>
        <?php if (!empty($errors)): ?>
          <ul class="errors">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <div class="input-group">
          <input
            type="text"
            id="full_name"
            name="full_name"
            class="input-field"
            placeholder=""
            maxlength="100"
            required
            value="<?= htmlspecialchars($full_name ?? '') ?>"
          />
          <label for="full_name" class="floating-label">Full Name</label>
        </div>

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
            minlength="10"
            required
          />
          <label for="password" class="floating-label">Password</label>
        </div>

        <div class="input-group">
          <label for="profile_image">Profile picture</label>
          <input type="file" id="profile_image" name="profile_image" accept="image/*" />
        </div>

        <button type="submit" class="form-btn form-btn--submit">Register</button>
      </form>

      <div class="form-bottom">
        <p>Already have an account? <a href="login.php" class="form-link">Log in</a></p>
      </div>
    </div>
       <script src="js/main.js"></script>
  </body>
</html>
