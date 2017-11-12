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
    /**
     * Respond to the request
     * @param $request
     * @return string
     */
    public function respond($request)
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
     * Runs the query based on query builder selection and returns a table view of the results
     * @param $request
     * @return string
     */
    private function runQuery($request)
    {
        $awardsQueryBuilder = new AwardsQueryBuilder();
        try {
            $groupBy = !empty($request['createChart']) && !empty($request['group-by-1'])
                ? ['group-by-1' => $request['group-by-1']] : [];
            $selectFields = isset($request['selectQueryFields']) ? $request['selectQueryFields'] : [];
            $awards = $awardsQueryBuilder->runQuery(json_decode($request['rules'], true), $selectFields, $groupBy);
        } catch (\Exception $exception) {
            header('HTTP/1.1 500 Internal Server Error');
            echo '<div class="alert alert-danger">' . $exception->getMessage() . '</div>';
            exit();
        }

        if (!empty($request['csvExport'])) {
            return $this->exportToCsv($awards, $selectFields);
        }

        if (!empty($request['createChart'])) {
            echo '<pre>';
            var_dump($awards);
            echo '</pre>';
            return;
        }

        return ReportsViews::resultsTableView($awards, $selectFields);
    }

    /**
     * Exports the query data to csv for the user
     *
     * @param $awards
     * @param $selectFields
     * @return string
     */
    private function exportToCsv($awards, $selectFields)
    {
        // Note: I utilized the following stack over flow and the php documentation comments
        // on fputcsv to come up with code
        // https://stackoverflow.com/questions/13316293/my-csv-export-displaying-html-how-to-get-rid-of
        ob_end_clean();

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=query-results.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $out = fopen('php://output', 'w');

        // add the header row
        $header = [];
        foreach ($selectFields as $columnKey) {
            $header[] = ReportsViews::$fields[$columnKey]['label'];
        }
        fputcsv($out, $header);

        foreach ($awards as $award) {
            fputcsv($out, $award);
        }

        fclose($out);

        exit();
    }
}