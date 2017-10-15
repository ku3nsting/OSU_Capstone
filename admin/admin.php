<?php

require_once __DIR__ . '/../Views/BaseTemplateView.php';
use views\BaseTemplateView;

echo BaseTemplateView::baseTemplateView('admin', 'Welcome to Admin Home');
