# Online Event Management System

A web-based event management system built from scratch with **PHP and MySQL** (backend) and **HTML5, CSS3 and JavaScript** (frontend). No CSS frameworks and no CMS were used.

**Student name:** _your name here_
**Student number:** _your student number here_
**Course / Module:** _your module here_

---

## 1. Requirements

- XAMPP (or WAMP/MAMP) with **Apache** and **MySQL** running
- PHP 8.0 or higher (the `fileinfo` extension must be enabled; it is on by default in XAMPP)
- A modern web browser

---

## 2. Installation

1. Copy the project folder `event-system` into your web server folder:
   `C:\xampp\htdocs\event-system\`
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Click the **Import** tab, choose `sql/database.sql`, and click **Go**.
   This creates the database `event_system` and its four tables.
5. Check the database settings in `includes/db.php`. The defaults for XAMPP are:
   - host: `localhost`
   - database: `event_system`
   - user: `root`
   - password: _(empty)_
6. Make sure the `uploads/` folder exists and is writable.
7. Open the system in your browser:
   `http://localhost/event-system/register.php`

---

## 3. Project structure

```
event-system/
├── includes/
│   ├── db.php              Database connection (PDO)
│   └── auth.php            Login check used by every restricted page
├── css/
│   └── style.css           All styling (written by hand)
├── js/
│   └── main.js             Form validation, image preview, live search
├── images/                 Decorative images and default profile picture
├── uploads/                Uploaded profile pictures (.htaccess blocks PHP execution)
├── sql/
│   └── database.sql        Database structure
├── register.php            User registration
├── login.php               Login
├── logout.php              Logout
├── forgot_password.php     Request a password reset link
├── reset_password.php      Choose a new password
├── dashboard.php           Upcoming events
├── event_create.php        Create an event
├── event_edit.php          Edit an event
├── event_delete.php        Delete an event
├── profile.php             Own profile (view and update)
├── user.php                View-only profile of another user
├── search.php              Search users and events
├── search_live.php         Live search endpoint used by JavaScript
├── messages.php            Private messaging
└── README.md               This file
```

---

## 4. Database

The database `event_system` has four tables:

| Table             | Purpose                                                        |
|-------------------|----------------------------------------------------------------|
| `users`           | Full name, unique email, hashed password, profile picture      |
| `events`          | Title, description, event date and time, owner, created time   |
| `messages`        | Private messages (sender, receiver, body, sent time)           |
| `password_resets` | Hashed reset tokens with an expiry time                        |

---

## 5. User guide

### Register and log in
1. Open `register.php`, enter your full name, email, a password of at least 10 characters, and optionally a profile picture (JPG, PNG or WEBP, maximum 2 MB).
2. After registering, log in on `login.php`.
3. Use **Log out** on the dashboard to end your session.

### Forgot password
1. Click **Forgot password?** on the login page and enter your email.
2. A reset link appears on screen. On a real server this link would be emailed; **email sending is simulated for local development**.
3. Open the link and choose a new password. The link expires after one hour and can only be used once.

### Events (dashboard)
- The dashboard lists **upcoming events**, soonest first, with the creator and the time each event was posted.
- Click **Create event** to add an event (title, description, date and time in the future).
- **Edit** and **Delete** appear only on your own events.

### Profile and search
- **My profile** shows your details and lets you change your name and profile picture.
- **Search** finds users and events. Results update live as you type.
- Clicking a user opens a **view-only profile** (name, picture, upcoming events). Email addresses are never shown to other users.

### Private messages
- Open a user's profile and click **Send message**.
- **Messages** lists your conversations. Your messages appear on the right and the other person's on the left, in time order.
- You can only read conversations you are part of.

---

## 6. Security measures

- **Prepared statements** (PDO) for every database query, with real prepared statements enabled.
- **Password hashing** with `password_hash()` and `password_verify()`.
- **XSS protection:** all output is escaped with `htmlspecialchars()`.
- **Input validation** on the server for every form, plus JavaScript validation for convenience.
- **Secure file uploads:** size limit, real file type check with `finfo`, allow-list of image types, random file names, and `uploads/.htaccess` to stop PHP running in the uploads folder.
- **Restricted pages:** every private page starts with `includes/auth.php`, which redirects visitors who are not logged in.
- **Access control:** event edit and delete check ownership in the SQL query; message queries always include the logged-in user's id; the sender of a message is taken from the session, never from the form.
- **Sessions:** `session_regenerate_id()` on login and full session destruction on logout.
- **Password reset tokens** are random, stored only as a hash, expire after one hour, and are deleted after use.

---

## 7. Technologies

- PHP (PDO) and MySQL
- HTML5, CSS3 (no frameworks)
- JavaScript (no libraries)

---

## 8. Known limitations

- Password reset emails are simulated: the link is shown on screen instead of being emailed.
- Messages are not refreshed automatically; reload the page to see new messages.
- Uploaded images are limited to 2 MB.
