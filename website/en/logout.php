<?php
// Only logged in users can log out
if (!$_COOKIE['logged_in']) {
  header('Location: /en/home.php');
  exit;
}

session_start();

// Delete session cookies
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
    setcookie('logged_in', 0,  time() + 60 * 60);
}

session_destroy();
header('Location: /en/home.php');
exit();
?>