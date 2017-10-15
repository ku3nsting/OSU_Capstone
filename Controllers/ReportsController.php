<?php
/**
 * User: jmueller
 * Date: 7/10/17
 * Time: 4:42 PM
 */

namespace controllers;


use views\BaseTemplateView;

require_once 'BaseController.php';
require_once __DIR__ . '/../Views/ReportsViews.php';
require_once __DIR__ . '/../Views/BaseTemplateView.php';

class ReportsController extends BaseController
{
    function respond($request)
    {
        switch ($request['action']) {
            case 'index':
            default:
                return self::index();
                break;
        }
    }

    /**
     * Return the Admin Reports Page
     * @return string
     */
    private function index()
    {
        // make necessary queries calls through models
        // return views related to the initial reports landing page
        return BaseTemplateView::baseTemplateView(
            'admin',
            '<div id="builder"></div>',
            'report.init();'
        );
    }
}