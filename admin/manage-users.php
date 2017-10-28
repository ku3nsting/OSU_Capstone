<?php

require_once __DIR__ . '/../Controllers/UsersController.php';

$usersController = new \controllers\UsersController();
try {
    $response = $usersController->respond($_REQUEST);
} catch (Exception $exception) {
    $response = $exception->getMessage();
}
echo $response;
