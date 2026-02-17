<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
session_start();

// Destroy session
session_unset();
session_destroy();

// Redirect to login
header('Location: login.php?logged_out=1');
exit;
