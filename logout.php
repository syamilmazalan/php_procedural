<?php

require 'config/config.php';

// Initialize session
session_start();

// Unset session variables
$_SESSION = array();

// Destroy session
session_destroy();

// Redirect to login
header('Location: ' . ROOT_URL . 'login.php');

exit;