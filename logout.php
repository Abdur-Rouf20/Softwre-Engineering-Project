<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Optional: Prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Redirect to login page
header("Location: index.php?msg=logged_out");
exit;
?>