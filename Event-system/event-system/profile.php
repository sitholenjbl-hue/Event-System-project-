<?php
require 'auth.php';
require 'db.php';
require 'functions.php';

$errors  = [];
$success = '';

$stmt = $pdo->prepare('SELECT id, full_name, email, profile_image, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');

    if ($full_name === '') {
        $errors[] = 'Please enter your name.';
    } elseif (strlen($full_name) > 10) {
        $errors[] = 'Your name must not exceed 100 characters.';
    }

    $new_image = null;
    if (empty($errors)) {
        $new_image = handle_image_upload($_FILES['profile_image'], $errors);
    }

    if (empty($errors)) {
        $image = $new_image ?? $user['profile_image'];  

        $pdo->prepare('UPDATE users SET full_name = ?, profile_image = ? WHERE id = ?')
            ->execute([$full_name, $image, $_SESSION['user_id']]);

        if ($new_image !== null && $user['profile_image']) {
            @unlink(__DIR__ . '/uploads/' . $user['profile_image']);
        }

        $_SESSION['full_name'] = $full_name;
        $user['full_name']     = $full_name;
        $user['profile_image'] = $image;
        $success = 'Profile updated.';
    }
}
?>