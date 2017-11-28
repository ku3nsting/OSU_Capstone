<?php

require_once __DIR__ . '/../Controllers/ReportsController.php';

session_start();
if (empty($_SESSION['authenticated'])) {
    echo "<script>location.href='/admin/login.php';</script>;";
    exit();
}

$reportsController = new \controllers\ReportsController();
echo $reportsController->respond($_REQUEST);
