<?php
require 'auth.php';
require 'db.php';

$errors = [];
$title = $description = $event_date = '';

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

    // the datetime-local input sends e.g. 2026-11-05T14:30
    $dt = DateTime::createFromFormat('Y-m-d\TH:i', $event_date);
    if (!$dt) {
        $errors[] = 'Please choose a valid date and time.';
    } elseif ($dt < new DateTime()) {
        $errors[] = 'The event must be in the future.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'INSERT INTO events (user_id, title, description, event_date) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$_SESSION['user_id'], $title, $description, $dt->format('Y-m-d H:i:s')]);
        header('Location: dashboard.php');
        exit;
    }
}
?>


  <div class="input-group">
    <input type="text" id="title" name="title" class="input-field" placeholder=""
           maxlength="150" required value="<?= htmlspecialchars($title) ?>">
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
           value="<?= htmlspecialchars($event_date) ?>">
  </div>

  <button type="submit" class="form-btn form-btn--submit">Create event</button>
</form>
<a href="dashboard.php">Back to dashboard</a>