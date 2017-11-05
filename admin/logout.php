<?php

//Code taken from PHP manual for session_destroy function
// http://php.net/manual/en/function.session-destroy.php
session_start();

// Unset all of the session variables.
$_SESSION = [];

// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

echo "<script>location.href='/admin/login.php';</script>;";

