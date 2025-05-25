<?php
require_once 'includes/auth.php';

// Logout the user
Auth::logout();

// Redirect to home page
header('Location: /');
exit;
?>