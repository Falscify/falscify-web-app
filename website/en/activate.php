<?php
require '/secrets/config.php';
// TODO User activation
// If email and secret_token are not in variables
// passed through GET, redirect to home page
if (!isset($_GET['email'], $_GET['secret_token'])) {
  header('Location: /en/home.php');
  exit;
}

// Establish connection
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Check for connection to DB
if (mysqli_connect_errno()) {
  ?>
  Error: failed to connect to the database!
  <?php
  echo mysqli_connect_errno();
  ?>
    <a href="/en/home.php">Return to home page.</a>
  <?php
  exit;
}

// Prepare statement
$sql_check_query = 'SELECT `email`, `active`, `secret_token` FROM accounts WHERE `email` = ?';
if ($stmt = $con->prepare($sql_check_query)) {
  $stmt->bind_param('s', $_GET['email']);
  $stmt->execute();
  $stmt->store_result();

  // Check if user exists
  if ($stmt->num_rows() === 0) {
    ?>
      Error: user with this e-mail doesn't exist!
      <a href="/en/home.php">Return to home page.</a>
    <?php
    exit;
  }

  // Fetch user data from database
  $stmt->bind_result($email, $active, $secret_token);
  $stmt->fetch();

  // Check if user is active
  if ($active) {
    ?>
      Error: this account is already active!
      <a href="/en/home.php">Return to home page.</a>
    <?php
    exit;
  }

  // Check if secret tokens match
  if ($_GET['secret_token'] !== $secret_token) {
    ?>
    Error: Incorrect secret token!
    <a href="/en/home.php">Return to home page.</a>
    <?php
    exit;
  }
} else {
  ?>
  Error: failed to submit request to the database!
  <?php
  echo mysqli_connect_errno();
  ?>
    <a href="/en/home.php">Return to home page.</a>
  <?php
  exit;
}
$stmt->close();

// If all above checks passed, change user's
// `active` value to 1 in the database,
// generate new `secret_token` and update
// it in the database as well
$sql_update_query = 'UPDATE accounts SET `active` = 1, `secret_token` = ? WHERE `email` = ?';
$new_secret_token = bin2hex(random_bytes(16));
if ($stmt = $con->prepare($sql_update_query)) {
  $stmt->bind_param('ss', $new_secret_token, $_GET['email']);
  $stmt->execute();
}
$stmt->close();
header('Location: /en/home.php');
exit;
?>
