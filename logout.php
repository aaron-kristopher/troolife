<?php
// Initialize the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = array();

session_destroy();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header("Location: login.php");
exit;
?>