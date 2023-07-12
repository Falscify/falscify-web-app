<?php
session_start();
// Set logged_in to 0 on session start and reload the page
// to avoid any warnings popping up on the page
if (!isset($_COOKIE['logged_in'])) {
  setcookie('logged_in', 0,  time() + 60 * 60);
  header('Location: '.$_SERVER['PHP_SELF']);
  exit;
}
// If user is logged in prolong cookie when entering new page
if ($_COOKIE['logged_in']) {
  setcookie('logged_in', 1,  time() + 60 * 60);
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>falscify | Explore</title>

  <link rel="icon" type="image/png" sizes="32x32" href="/resources/graphics/logotype.png">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="/resources/css/main.css">
</head>

<body>
  <!-- HEADER -->
  <div class="page-container">
    <div class="content-wrap">
      <header class="header" id="header">
        <nav class="navbar container">
          <?php include('/en/html/logo.html'); ?>
          <div class="menu" id="menu">
            <ul class="list">
              <li class="list-item">
                <a href="/en/home.php" class="list-link">Home</a>
              </li>
              <li class="list-item">
                <a href="/en/recent.php" class="list-link">Recent</a>
              </li>
              <li class="list-item">
                <a href="/en/explore.php" class="list-link current">Explore</a>
              </li>
              <li class="list-item">
                <a href="/en/about.php" class="list-link">About Us</a>
              </li>
              <?php if ($_COOKIE['logged_in']) { ?>
                <li class="list-item screen-l-hidden">
                  <a href="/en/account.php" class="list-link">Account</a>
                </li>
                <li class="list-item screen-l-hidden">
                  <a href="/en/logout.php" class="list-link">Sign out</a>
                </li>
              <?php } else { ?>
                <li class="list-item screen-l-hidden">
                  <a href="/en/login.php" class="list-link">Sign in</a>
                </li>
                <li class="list-item screen-l-hidden">
                  <a href="/en/register.php" class="list-link">Sign up</a>
                </li>
              <?php } ?>
            </ul>
          </div>
          <div class="list list-right">
            <button class="btn place-items-center" id="theme-toggle-btn" aria-label="auto">
              <i class="ri-sun-line sun-icon"></i>
              <i class="ri-moon-line moon-icon"></i>
            </button>
            <button class="btn place-items-center screen-l-hidden menu-toggle-icon" id="menu-toggle-icon">
              <i class="ri-menu-3-line open-menu-icon"></i>
              <i class="ri-close-line close-menu-icon"></i>
            </button>
            <?php if ($_COOKIE['logged_in']) { ?>
              <button type="button" class="btn login-btn screen-s-hidden">
                <a href="/en/account.php">Account</a>
              </button>
              <button type="button" class="btn logout-btn screen-s-hidden">
                <a href="/en/logout.php">Sign out</a>
              </button>
            <?php } else { ?>
              <button class="btn register-btn screen-s-hidden">
                <a href="/en/register.php">Sign up</a>
              </button>
              <button type="button" class="btn login-btn screen-s-hidden">
                <a href="/en/login.php">Sign in</a>
              </button>
            <?php } ?>
          </div>
        </nav>
      </header>
      <!-- TODO Science Tree -->
      <!-- TODO Tree view  -->
      <!-- TODO Buttons view -->
      <div class="container wip">Currently working on it...</div>
    </div>
    <!-- FOOTER -->
    <?php include('/en/html/footer.html'); ?>
  </div>

  <!-- JS SCRIPTS -->
  <script src="/resources/js/main.js"></script>
</body>
</html>