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
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
// Fetch countries from database
$sql_fetch_countries_query = 'SELECT `country_name` FROM countries';
if ($stmt = mysqli_query($con, $sql_fetch_countries_query)) {
  $countries = $stmt->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
} else {
  ?>
  <div class="message-container error-container">Error: failed to submit query to the database.</div>
  <?php
}
// Fetch institutions info from database
$sql_fetch_institutions_query = 'SELECT `institution_name` FROM institutions';
if ($stmt = mysqli_query($con, $sql_fetch_institutions_query)) {
  $institutions = $stmt->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
} else {
  ?>
  <div class="message-container error-container">Error: failed to submit query to the database.</div>
  <?php
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>falscify | Sign up</title>

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
                  <a href="/en/login.php" class="list-link">Sign in</a>
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
              <button type="button" class="btn login-btn screen-s-hidden">
                <a href="/en/login.php">Sign in</a>
              </button>
            <?php } ?>
          </div>
        </nav>
      </header>

      <!-- Register form -->
      <div class="form-container">
        <h2>Sign up</h2>
        <app-orcid-btn></app-orcid-btn>

        <?php
        // TODO Remove echo calls and change JS redirect to header()

        include('/en/html/orcid_button.html');
        // Upon clicking sumbit button
        if (isset($_POST['submit'])) {
          $error = FALSE;

          // Sanitize inputs
          $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_SPECIAL_CHARS);
          $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_SPECIAL_CHARS);
          $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
          $user_institution = filter_input(INPUT_POST, 'institution', FILTER_SANITIZE_SPECIAL_CHARS);
          $user_country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_SPECIAL_CHARS);

          // Check if user selected the institution
          if (!isset($_POST['institution'])) {
            ?>
            <div class="message-container error-container">Please select your institution.</div>
            <?php
            $error = TRUE;
          } else {
            // Sanitize
            $institution = filter_input(INPUT_POST, 'institution', FILTER_SANITIZE_SPECIAL_CHARS);
          }

          // Check if passwords match
          if ($_POST['password'] !== $_POST['repeat_password']) {
            ?>
            <div class="message-container error-container">Passwords don't match!</div>
            <?php
            $error = TRUE;
          }

          // Establish DB connection
          $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

          // Check for connection to DB
          if (mysqli_connect_errno()) {
            ?>
            <div class="message-container error-container">Error: failed to connect to the database.</div>
            <?php
          }

          // Check if user already exists
          $sql_check_query = 'SELECT `email` FROM accounts WHERE `email` = ?';
          if ($stmt = $con->prepare($sql_check_query)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
              ?>
              <div class="message-container error-container">User with this e-mail already exists!</div>
              <?php
              $error = TRUE;
            } else {
              $stmt->close();
            }
          } else {
            ?>
            <div class="message-container error-container">Error: failed to submit query to the database.></div>
            <?php
            $error = TRUE;
          }

          // If no error is encountered proceed with registration
          if (!$error) {
            $hashed_password = trim(password_hash($_POST['password'], PASSWORD_DEFAULT));
            $secret_token = bin2hex(random_bytes(16));
            $sql_insert_query = 'INSERT INTO accounts (`first_name`, `last_name`, `country_name`, `institution_name`, `email`, `password`, `secret_token`) VALUES (?, ?, ?, ?, ?, ?, ?)';
            if ($stmt = $con->prepare($sql_insert_query)) {
              $stmt->bind_param('sssssss', $first_name, $last_name, $user_country, $user_institution, $email, $hashed_password, $secret_token);
              $stmt->execute();
              $stmt->close();

              // Send activation email
              $activation_url = $APP_URL . '/activate?email=' . $email . '&secret_token=' . $secret_token;
              $email_message = <<<MESSAGE
              Thank you for signing up to Falscify!
              To confirm your registration please click the following link:
              $activation_url
              Best regards,
              Falscify team
              MESSAGE;
              $email_header = 'From: ' . $SENDER_EMAIL;
              $email_subject = 'Falscify - Confirm your registration';

              // TODO Set up activation email once site goes live
              // We should edit ../../svn_repo/config.php to achieve this
              // ~f1lem0n
              if(mail($email, $email_subject , nl2br($email_message), $email_header)) {
                echo '<div class="message-container success-container">Success! Check your inbox for our confirmation email.</div>';
              } else {
                echo '<div class="message-container error-container">Failed to send activation e-mail.</div>';
              }

              // Redirect after successful registration
              setcookie('registered', 1, time() + 30);
              header('Location: /en/home.php');
              exit;
            } else {
              ?>
              <div class="message-container error-container">Error: failed to submit query to the database.</div>
              <?php
            }
          }
        }
        ?>

        <form class="register-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" , method="POST">
          <label>First name</label>
          <input required type="text" name="first_name" id="first_name" placeholder="Enter first name" value=<?php echo isset($first_name) ? $first_name : ''; ?>>
          <label>Last name</label>
          <input required type="text" name="last_name" id="last_name" placeholder="Enter last name" value=<?php echo isset($last_name) ? $last_name : ''; ?>>

          <label>Country of origin</label>
          <select required name="country" class="form-list"
            value="<?php echo isset($user_country) ? $user_country : ''; ?>">
            <option disabled selected>Choose country</option>
            <?php foreach ($countries as $country): ?>
            <option> <?= htmlspecialchars($country['country_name']); ?> </option>
            <?php endforeach ?>
          </select>

          <label>Institution</label>
          <select required name="institution" class="form-list"
            value="<?php echo isset($institution) ? $institution : ''; ?>">
            <option disabled selected>Choose institution</option>
            <?php foreach ($institutions as $institution): ?>
            <option> <?= htmlspecialchars($institution['institution_name']); ?> </option>
            <?php endforeach ?>
          </select>

          <label>E-mail</label>
          <input required type="email" name="email" id="email" placeholder="Enter e-mail" value=<?php echo isset($email) ? $email : ''; ?>>
          <label>Password</label>
          <input required type="password" name="password" id="password" placeholder="Enter password" value=<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>>
          <label>Repeat password</label>
          <input required type="password" name="repeat_password" id="repeat_password" placeholder="Repeat password"
            value=<?php echo isset($_POST['repeat_password']) ? $_POST['repeat_password'] : ''; ?>>
          <fieldset>
            <input required id="accept-terms" type="checkbox">
            <label for="accept-terms">I have read and accept the <a target="_blank" href="terms.php"
                class="text-link">Terms of Use.</a>
            </label>
          </fieldset>

          <label hidden>Sign up</label>
          <button name="submit" type="submit" class="btn register-btn">Sign up</button>
        </form>

        <div>
          Already have an account?
          <a class="text-link" href="/en/login.php">
            Sign in.
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
</html>
