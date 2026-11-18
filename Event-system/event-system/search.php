<?php
require 'auth.php';
require 'db.php';

$q      = trim($_GET['q'] ?? '');
$users  = [];
$events = [];

if ($q !== '') {

    $safe = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
    $like = '%' . $safe . '%';

    $stmt = $pdo->prepare(
        'SELECT id, full_name, profile_image FROM users WHERE full_name LIKE ? ORDER BY full_name LIMIT 20'
    );
    $stmt->execute([$like]);
    $users = $stmt->fetchAll();

    $stmt = $pdo->prepare(
        'SELECT id, title, event_date FROM events
         WHERE (title LIKE ? OR description LIKE ?) AND event_date >= ?
         ORDER BY event_date ASC LIMIT 20'
    );
    $stmt->execute([$like, $like, date('Y-m-d H:i:s')]);
    $events = $stmt->fetchAll();
}
?>

<?php
// ...your PHP block at the top stays as it is...
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Search</title>
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <h1>Search</h1>

    <!-- live search: new lines -->
    <input type="text" id="live-q" placeholder="Search users or events">
    <div id="live-results"></div>

    <!-- your old search form and the Users / Events results stay as they are -->
    <form method="get" action="search.php">
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search users or events">
      <button type="submit">Search</button>
    </form>

    <h3>Users</h3>
    <?php foreach ($users as $u): ?>
      <p><a href="user.php?id=<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['full_name']) ?></a></p>
    <?php endforeach; ?>

    <h3>Events</h3>
    <?php foreach ($events as $e): ?>
      <p><?= htmlspecialchars($e['title']) ?> (<?= date('d M Y, H:i', strtotime($e['event_date'])) ?>)</p>
    <?php endforeach; ?>

    <a href="dashboard.php">Back</a>

    <!-- script tag: new line, last thing before </body> -->
    <script src="js/main.js"></script>
  </body>
</html>