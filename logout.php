<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION = [];
session_destroy();
header('Location: /drop/tecnoloweb2/login.php');
exit;
