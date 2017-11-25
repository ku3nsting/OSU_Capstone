<?php

require_once __DIR__ . '/../Views/BaseTemplateView.php';
require_once __DIR__ . '/common.php';
use views\BaseTemplateView;

session_start();
if (empty($_SESSION['authenticated'])) {
    echo "<script>location.href='/admin/login.php';</script>;";
    exit();
}

$firstDayOfYear = new DateTime('first day of January ' . date('Y'));
$dateString = $firstDayOfYear->format('Y-m-d');
$yearString = $firstDayOfYear->format('Y');

echo BaseTemplateView::baseTemplateView(
    'admin',
    BaseTemplateView::homeView(),
    "admin.adminCharts('$dateString', '$yearString');"
);
