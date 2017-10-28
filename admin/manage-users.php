<?php

require_once __DIR__ . '/../Controllers/UsersController.php';

$usersController = new \controllers\UsersController();
echo $usersController->respond($_REQUEST);
