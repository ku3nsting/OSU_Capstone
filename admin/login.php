<?php

require_once __DIR__ . '/../Controllers/LoginController.php';

$loginController = new \controllers\LoginController();
try {
    $response = $loginController->respond($_REQUEST);
} catch (Exception $exception) {
    http_response_code(500);
    $response = '<div class="alert alert-danger">' . $exception->getMessage() . '</div>';
}
echo $response;
