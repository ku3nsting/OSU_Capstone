<?php

require_once __DIR__ . '/../Views/BaseTemplateView.php';
use views\BaseTemplateView;

session_start();
if (empty($_SESSION['authenticated'])) {
    echo "<script>location.href='/admin/login.php';</script>;";
    exit();
}

echo BaseTemplateView::baseTemplateView('admin', BaseTemplateView::homeView(), 'admin.adminCharts();');
