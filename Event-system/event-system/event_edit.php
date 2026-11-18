<?php
require 'auth.php';
require 'db.php';


$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);


$stmt = $pdo->prepare(
    'SELECT id, title, description, event_date FROM events WHERE id = ? AND user_id = ?'
);
$stmt->execute([$id, $_SESSION['user_id']]);
$event = $stmt->fetch();


if (!$event) {
    header('Location: dashboard.php');
    exit;
}

$errors        = [];
$title         = $event['title'];
$description   = $event['description'] ?? '';
$original_date = date('Y-m-d\TH:i', strtotime($event['event_date']));
$event_date    = $original_date;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date  = $_POST['event_date'] ?? '';

    if ($title === '') {
        $errors[] = 'Please enter a title.';
    } elseif (strlen($title) > 150) {
        $errors[] = 'The title must not exceed 150 characters.';
    }

    if (strlen($description) > 2000) {
        $errors[] = 'The description is too long.';
    }

    $dt = DateTime::createFromFormat('Y-m-d\TH:i', $event_date);
    if (!$dt) {
        $errors[] = 'Please choose a valid date and time.';
    } elseif ($event_date !== $original_date && $dt < new DateTime()) {

        $errors[] = 'The new date must be in the future.';
    }

    if (empty($errors)) {
        
        $stmt = $pdo->prepare(
            'UPDATE events SET title = ?, description = ?, event_date = ?
             WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([
            $title,
            $description,
            $dt->format('Y-m-d H:i:s'),
            $id,
            $_SESSION['user_id']
        ]);

        header('Location: dashboard.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Event - Online Event Management System</title>
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <h1>Edit event</h1>

    <form class="form-box" method="post" action="event_edit.php">

      <?php if (!empty($errors)): ?>
        <ul class="errors">
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <input type="hidden" name="id" value="<?= (int)$id ?>" />

      <div class="input-group">
        <input type="text" id="title" name="title" class="input-field" placeholder=""
               maxlength="150" required value="<?= htmlspecialchars($title) ?>" />
        <label for="title" class="floating-label">Event title</label>
      </div>

      <div class="input-group">
        <textarea id="description" name="description" class="input-field" placeholder=""
                  maxlength="2000"><?= htmlspecialchars($description) ?></textarea>
        <label for="description" class="floating-label">Description</label>
      </div>

      <div class="input-group">
        <label for="event_date">Date and time</label>
        <input type="datetime-local" id="event_date" name="event_date" required
               value="<?= htmlspecialchars($event_date) ?>" />
      </div>

      <button type="submit" class="form-btn form-btn--submit">Save changes</button>
    </form>

    <a href="dashboard.php">Cancel and go back</a>
  </body>
</html>