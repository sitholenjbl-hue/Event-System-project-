<?php
require 'auth.php';
require 'db.php';

$q = trim($_GET['q'] ?? '');
if ($q === '') exit;

$like = '%' . $q . '%';

$stmt = $pdo->prepare('SELECT id, full_name FROM users WHERE full_name LIKE ? LIMIT 10');
$stmt->execute([$like]);
foreach ($stmt->fetchAll() as $u) {
    echo '<p><a href="user.php?id=' . (int)$u['id'] . '">' . htmlspecialchars($u['full_name']) . '</a></p>';
}

$stmt = $pdo->prepare('SELECT title, event_date FROM events WHERE title LIKE ? ORDER BY event_date ASC LIMIT 10');
$stmt->execute([$like]);
foreach ($stmt->fetchAll() as $e) {
    echo '<p>' . htmlspecialchars($e['title']) . ' (' . date('d M Y, H:i', strtotime($e['event_date'])) . ')</p>';
}