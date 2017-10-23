<?php
/**
 * User: jmueller
 * Date: 7/10/17
 * Time: 4:42 PM
 */

namespace controllers;


use database\AwardsQueryBuilder;
use views\BaseTemplateView;
use views\ReportsViews;

require_once 'BaseController.php';
require_once __DIR__ . '/../Views/ReportsViews.php';
require_once __DIR__ . '/../Views/BaseTemplateView.php';
require_once __DIR__ . '/../Database/Query/AwardsQueryBuilder.php';

class ReportsController extends BaseController
{
    function respond($request)
    {
        switch ($request['action']) {
            case 'run-query':
                return self::runQuery($request);
                break;
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
            ReportsViews::indexView(),
            'report.init();'
        );
    }

    /**
     * @param $request
     */
    private function runQuery($request)
    {

        $awardsQueryBuilder = new AwardsQueryBuilder();
        try {
            $selectFields = isset($request['selectQueryFields']) ? $request['selectQueryFields'] : [];
            $awards = $awardsQueryBuilder->runQuery(json_decode($request['rules'], true), $selectFields);
        } catch (\Exception $exception) {
            header('HTTP/1.1 500 Internal Server Error');
            echo $exception->getMessage();
            exit();
        }

        echo '<pre>';
        var_dump($awards);
        echo '</pre>';
    }
}