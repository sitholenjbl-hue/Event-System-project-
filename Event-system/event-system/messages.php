<?php
require 'auth.php';
require 'db.php';

$me   = $_SESSION['user_id'];
$with = (int)($_GET['with'] ?? $_POST['with'] ?? 0);
$errors = [];
$other  = null;

if ($with > 0) {
    $stmt = $pdo->prepare('SELECT id, full_name FROM users WHERE id = ?');
    $stmt->execute([$with]);
    $other = $stmt->fetch();

    if (!$other || $with === $me) {
        header('Location: messages.php');
        exit;
    }
}


if ($other && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = trim($_POST['body'] ?? '');

    if ($body === '' || strlen($body) > 1000) {
        $errors[] = 'Message must be 1 to 1000 characters.';
    } else {
    
        $pdo->prepare('INSERT INTO messages (sender_id, receiver_id, body) VALUES (?, ?, ?)')
            ->execute([$me, $with, $body]);
        header('Location: messages.php?with=' . $with);
        exit;
    }
}

if ($other) {

    $stmt = $pdo->prepare(
        'SELECT sender_id, body, sent_at FROM messages
         WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
         ORDER BY sent_at ASC, id ASC'
    );
    $stmt->execute([$me, $with, $with, $me]);
    $messages = $stmt->fetchAll();
} else {
    
    $stmt = $pdo->prepare(
        'SELECT id, full_name FROM users WHERE id IN
           (SELECT receiver_id FROM messages WHERE sender_id = ?
            UNION
            SELECT sender_id FROM messages WHERE receiver_id = ?)'
    );
    $stmt->execute([$me, $me]);
    $chats = $stmt->fetchAll();
}
?>

<?php if ($other): ?>
  <h1>Chat with <?= htmlspecialchars($other['full_name']) ?></h1>

  <div class="chat">
    <?php foreach ($messages as $m): ?>
      <div class="bubble <?= $m['sender_id'] == $me ? 'mine' : 'theirs' ?>">
        <?= nl2br(htmlspecialchars($m['body'])) ?>
        <small><?= date('d M Y, H:i', strtotime($m['sent_at'])) ?></small>
      </div>
    <?php endforeach; ?>
  </div>

  <?php foreach ($errors as $error): ?>
    <p class="errors"><?= htmlspecialchars($error) ?></p>
  <?php endforeach; ?>

  <form method="post" action="messages.php">
    <input type="hidden" name="with" value="<?= (int)$with ?>">
    <textarea name="body" maxlength="1000" required></textarea>
    <button type="submit">Send</button>
  </form>
  <a href="messages.php">All conversations</a>

<?php else: ?>
  <h1>Messages</h1>
  <?php if (empty($chats)): ?>
    <p>No conversations yet. Open a user's profile and click "Send message".</p>
  <?php endif; ?>
  <?php foreach ($chats as $c): ?>
    <p><a href="messages.php?with=<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['full_name']) ?></a></p>
  <?php endforeach; ?>
  <a href="dashboard.php">Back</a>
<?php endif; ?>
