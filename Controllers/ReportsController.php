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

        // If generate chart data, validate chart params
        if (!empty($request['createChart']) && (empty($request['group-by-1']) || empty($request['chart-type']))) {
            return $this->respondWithErrors(['Error: Group By and Chart Type are required fields'], 422);
        }
        $groupBy = [];
        if (!empty($request['createChart'])) {
            $groupBy = [
                'group-by-1' => !empty($request['group-by-1']) ? $request['group-by-1'] : ''
            ];

            if (in_array($request['chart-type'], ['bar'])) {
                $groupBy['group-by-2'] = !empty($request['group-by-2']) ? $request['group-by-2'] : '';
            }
        }

        // Set the select fields and run the query (this can be empty for chart)
        $selectFields = isset($request['selectQueryFields']) ? $request['selectQueryFields'] : [];
        $awards = $awardsQueryBuilder->runQuery(json_decode($request['rules'], true), $selectFields, $groupBy);

        if (!empty($request['csvExport'])) {
            return $this->exportToCsv($awards, $selectFields);
        }

        if (!empty($request['createChart'])) {
            $response['noData'] = empty($awards);
            $response['noDataMsg'] = empty($awards) ? '<div class="alert alert-info">No awards given for the specified filters</div>' : '';
            switch ($request['chart-type']) {
                case 'bar':
                    if (!empty($groupBy['group-by-2'])) {
                        list($seriesLabels, $results) = $this->processData($awards);
                        $response['data'] = ReportsViews::groupByTableWithSeriesView($results, $seriesLabels);
                    } else {
                        $response['data'] = ReportsViews::groupByTableView($awards);
                    }
                    break;
                case 'line':
                    $response['data'] = $this->lineChartData($awards);
                    break;
                case 'pie':
                     $response['data'] = $this->pieChartData($awards);
                    break;
            }
            return json_encode($response);
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

    /**
     * @param $awards
     * @return array
     */
    private function lineChartData($awards)
    {
        $chartData = [
            'categories' => array_column($awards, 'label'),
            'data' => array_column($awards, 'count')
        ];
        return $chartData;
    }

    /**
     * @param $awards
     * @return array
     */
    private function pieChartData($awards)
    {
        $data = [];
        foreach($awards as $award) {
            $data[] = ['name' => $award['label'], 'y' => $award['count']];
        }
        return $data;
    }

    /**
     * @param $results
     * @return array
     */
    private function processData($results)
    {
        $seriesLabels = array_unique(array_column($results, 'seriesLabel'));

        $newResults = [];
        foreach ($results as $result) {
            $newResults[$result['label']][$result['seriesLabel']] = $result['count'];
        }

        return [$seriesLabels, $newResults];
    }
}