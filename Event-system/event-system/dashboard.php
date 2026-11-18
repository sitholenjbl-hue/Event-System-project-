<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'auth.php';
require 'db.php';


$stmt = $pdo->prepare(
    'SELECT e.id, e.user_id, e.title, e.description, e.event_date, e.created_at, u.full_name
     FROM events e
     JOIN users u ON u.id = e.user_id
     WHERE e.event_date >= ?
     ORDER BY e.event_date ASC'
);
$stmt->execute([date('Y-m-d H:i:s')]);
$events = $stmt->fetchAll();


?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Online Event Management System</title>
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>

    <nav>
      <a href="event_create.php">Create event</a>
      <a href="logout.php">Log out</a>
    </nav>

    <h2>Upcoming events</h2>

    <?php if (empty($events)): ?>
      <p>No upcoming events yet.</p>
    <?php else: ?>
      <?php foreach ($events as $event): ?>
        <div class="event-card">
          <h3><?= htmlspecialchars($event['title']) ?></h3>
          <p><?= htmlspecialchars($event['description']) ?></p>

          
          <p>Date: <?= date('d M Y, H:i', strtotime($event['event_date'])) ?></p>
          <p>Posted by <?= htmlspecialchars($event['full_name']) ?>
             on <?= date('d M Y, H:i', strtotime($event['created_at'])) ?></p>

          <?php if ($event['user_id'] == $_SESSION['user_id']): ?>
            <a href="event_edit.php?id=<?= (int)$event['id'] ?>">Edit</a>

            <form method="post" action="event_delete.php" style="display:inline"
                  onsubmit="return confirm('Delete this event?');">
              <input type="hidden" name="id" value="<?= (int)$event['id'] ?>">
              <button type="submit">Delete</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </body>
</html>