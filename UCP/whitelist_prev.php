<?php
session_start();
require_once 'steamauth/userInfo.php';

if (isset($_SESSION['whitelist_current']) && $_SESSION['whitelist_current'] > 0) {
    $_SESSION['whitelist_current']--;
}

header("Location: connect.php");
exit;
?>