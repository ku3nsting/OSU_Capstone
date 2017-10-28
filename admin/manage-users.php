<?php

require_once __DIR__ . '/../Controllers/UsersController.php';

$usersController = new \controllers\UsersController();
try {
    $response = $usersController->respond($_REQUEST);
} catch (Exception $exception) {
    http_response_code(500);
    $response = '<div class="alert alert-danger">' . $exception->getMessage() . '</div>';
}
echo $response;
