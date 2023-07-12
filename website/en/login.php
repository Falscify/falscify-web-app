<?php
session_start();
require '/secrets/config.php';
// Set logged_in to 0 on session start and reload the page
// to avoid any warnings popping up on the page
if (!isset($_COOKIE['logged_in'])) {
  setcookie('logged_in', 0,  time() + 60 * 60);
  header('Location: '.$_SERVER['PHP_SELF']);
  exit;
}
if ($_COOKIE['logged_in']) {
  setcookie('logged_in', 1,  time() + 60 * 60);
  header('Location: /en/home.php');
  exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>falscify | Sign in</title>

  <link rel="icon" type="image/png" sizes="32x32" href="/resources/graphics/logotype.png">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="/resources/css/main.css">
</head>

<body>
  <div class="page-container">
    <div class="content-wrap">
      <!-- HEADER -->
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
                <a href="/en/explore.php" class="list-link">Explore</a>
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
            <?php } ?>
          </div>
        </nav>
      </header>

      <!-- LOGIN FORM -->
      <div class="form-container">
        <h2>Sign in</h2>
        <?php
        include('/en/html/orcid_button.html');
        if (isset($_POST['submit'])) {
          $error = FALSE;
          $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

          // Establish DB connection
          $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

          // Check for connection to DB
          if (mysqli_connect_errno()) {
            ?>
            <div class="message-container error-container">Error: failed to connect to the database.</div>
            <?php
          }

          // Check user in database
          $sql_query = 'SELECT `user_id`, `email`, `password`, `first_name`, `last_name`, `credit_points`, `created_at`, `active`, `orcid_id` FROM accounts WHERE `email` = ?';
          if ($stmt = $con->prepare($sql_query)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows() === 0) {
              ?>
              <div class="message-container error-container">User with this e-mail doesn't exist!</div>
              <?php
              $error = TRUE;
            } else {
              $stmt->bind_result($user_id, $email, $password, $first_name, $last_name, $credit_points, $created_at, $active, $orcid_id);
              $stmt->fetch();
              if (!password_verify($_POST['password'], $password)) {
                ?>
                <div class="message-container error-container">Incorrect password!</div>
                <?php
                $error = TRUE;
              }
              if ($active !== 1) {
                ?>
                <div class="message-container error-container">Account not active! Please check your inbox for the activation link.</div>
                <?php
                $error = TRUE;
              }
            }
          } else {
            ?>
            <div class="message-container error-container">Error: failed to submit request to the database.</div>
            <?php
            $error = TRUE;
          }
          if (!$error) {
            $stmt->close();
            setcookie('logged_in', 1,  time() + 60 * 60);
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['credit_points'] = $credit_points;
            $_SESSION['created_at'] = $created_at;
            $_SESSION['active'] = $active;
            if ($orcid_id !== NULL) {
              $_SESSION['orcid_id'] = $orcid_id;
            }
            header('Location: /en/home.php');
            exit();
          }
        }
        ?>

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
          <label>E-mail</label>
          <input required type="email" name="email" id="email" placeholder="Enter e-mail" value=<?php echo isset($email) ? $email : ''; ?>>
          <label>Password</label>
          <input required type="password" name="password" id="password" placeholder="Enter password" value=<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>>
          <label hidden>Sign in</label>
          <button name="submit" type="submit" class="btn login-btn">Sign in</button>
        </form>

        <div>
          Forgot password?
          <a class="text-link" href="#">
            Send recovery e-mail.
          </a>
        </div>

        <br>

        <div>
          Don't have an account?
          <a class="text-link" href="/en/register.php">
            Sign up.
          </a>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
    <?php include('/en/html/footer.html'); ?>
  </div>

  <!-- JS SCRIPTS -->
  <script src="/resources/js/main.js"></script>
</body>