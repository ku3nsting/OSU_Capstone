<?php

require_once __DIR__ . '/../Controllers/UsersController.php';
require_once __DIR__ . '/common.php';

session_start();
if (empty($_SESSION['authenticated'])) {
    echo "<script>location.href='/admin/login.php';</script>;";
    exit();
}

$usersController = new \controllers\UsersController();
try {
    $response = $usersController->respond($_REQUEST);
} catch (Exception $exception) {
    $code = !empty($exception->getCode()) ? $exception->getCode() : 500;
    http_response_code($code);
    if ($code >= 500) {
        $response = '<div class="alert alert-danger">Oops something went wrong. Please contact your site administrator.</div>';
    } else {
        $response = '<div class="alert alert-danger">' . html($exception->getMessage()) . '</div>';
    }
}
echo $response;
