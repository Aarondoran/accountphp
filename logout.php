<?php
session_start();

// Destroy session
session_unset();
session_destroy();

// Clear cookie
setcookie('remember_login', '', time() - 3600, '/');

// Redirect
header("Location: home.php");
exit();
?>
