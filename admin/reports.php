<?php

require_once __DIR__ . '/../Controllers/ReportsController.php';

$reportsController = new \controllers\ReportsController();
echo $reportsController->respond($_REQUEST);
