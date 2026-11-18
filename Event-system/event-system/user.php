<?php
require 'auth.php';
require 'db.php';

$id = (int)($_GET['id'] ?? 0);

// deliberately does NOT select email or password_hash
$stmt = $pdo->prepare('SELECT id, full_name, profile_image, created_at FROM users WHERE id = ?');
$stmt->execute([$id]);
$profile = $stmt->fetch();

if (!$profile) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT title, event_date FROM events WHERE user_id = ? AND event_date >= ? ORDER BY event_date ASC'
);
$stmt->execute([$id, date('Y-m-d H:i:s')]);
$events = $stmt->fetchAll();
?>
<h1><?= htmlspecialchars($profile['full_name']) ?></h1>
<img src="..." alt="Profile picture" width="120">

<?php if ((int)$_GET['id'] !== $_SESSION['user_id']): ?>
  <p><a href="messages.php?with=<?= (int)$_GET['id'] ?>">Send message</a></p>
<?php endif; ?>

<a href="dashboard.php">Back</a>

