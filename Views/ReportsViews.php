<?php
/**
 * User: jmueller
 * Date: 7/10/17
 * Time: 4:54 PM
 */

namespace views;


use database\AwardsQueryBuilder;

class ReportsViews
{
    public static $fields = [
        "AwardLabel" => [
            "label" => "Award Type",
            "type" => "string",
        ],
        "AwardDate" => [
            "label" => "Award Date",
            "type" => "date",
        ],
        "Email" => [
            "label" => "Awardee Email",
            "type" => "string",
        ],
        "fName" => [
            "label" => "Awardee First Name",
            "type" => "string",
        ],
        "lName" => [
            "label" => "Awardee Last Name",
            "type" => "string",
        ],
        "hireDate" => [
            "label" => "Awardee Hire Date",
            "type" => "date",
        ],
        "GiverFirstName" => [
            "label" => "Giver First Name",
            "type" => "string",
        ],
        "GiverLastName" => [
            "label" => "Giver Last Name",
            "type" => "string",
        ],
        "GiverEmail" => [
            "label" => "Giver Email",
            "type" => "string",
        ]
    ];

    /**
     * Returns the view for the reports index page
     * @return string
     */
    public static function indexView()
    {
        $groupBySelectOptions = '';
        foreach (AwardsQueryBuilder::$groupByFields as $value => $groupByField) {
            $groupBySelectOptions .= "<option value='$value'>{$groupByField['option-label']}</option>";
        }

        return '
            <div id="msg"></div>
            <form id="selectQueryForm" action="/admin/reports.php">
                <h2>Report Query Builder</h2>
                <input type="hidden" value="run-query" id="action" name="action">
                <input type="hidden" value="" id="rules" name="rules">
                <input type="hidden" value="0" id="csvExport" name="csvExport">
                <input type="hidden" value="0" id="createChart" name="createChart">
                <div class="form-group">
                    <label for="selectQueryFields">* SELECT Fields</label>
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true" style="cursor: help;" title="To select multiple click and drag mouse or hold Control and Click with mouse. *Required with Generate Table or CSV"></span>
                    ' . self::selectQueryFields() . '
                </div>
                <label>* WHERE</label>
                <div id="builder"></div>
                <div class="form-group">
                    <a class="btn btn-primary btn-sm" href="#" role="button" onclick="report.runQuery();">Generate Table</a>
                    <a class="btn btn-primary btn-sm" href="#" role="button" onclick="report.exportCsv();">Export to CSV</a>
                </div>
                <div class="panel panel-default" style="border: 1px solid #DCC896;">
                    <div class="panel-heading" style="background: rgba(250,240,210,.5);">Charting Actions</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="chart-title">Chart Title</label>
                            <input id="chart-title" class="form-control" name="chart-title" type="text">
                        </div>
                        <div class="form-group">
                            <label for="group-by-1">* Group By</label>
                            <span class="glyphicon glyphicon-info-sign" aria-hidden="true" style="cursor: help;" title="*Required with Create Chart"></span>
                            <select id="group-by-1" name="group-by-1" class="form-control">
                                ' . $groupBySelectOptions . '
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="chart-type">* Chart Type</label>
                            <span class="glyphicon glyphicon-info-sign" aria-hidden="true" style="cursor: help;" title="*Required with Create Chart"></span>
                            <select id="chart-type" name="chart-type" class="form-control">
                                <option value="bar">Bar</option>
                                <option value="line">Line</option>
                                <option value="pie">Pie</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <a class="btn btn-primary btn-sm" href="javascript: void(0);" role="button" onclick="report.createChart()">Create Chart</a>
                        </div>
                    </div>
                </div>
            </form>
            <h2>Results</h2>
            <div id="query-results"></div>
            <div id="chart-container"></div>
        ';
    }

    /**
     * Returns the select query multi select field
     * @return string
     */
    public static function selectQueryFields()
    {
        $selectFields = "<select id='selectQueryFields' name='selectQueryFields[]' multiple class='form-control' style='height: 12.5em'>";
        foreach (self::$fields as $fieldId => $field) {
            $selectFields .= "<option value='{$fieldId}'>{$field['label']}</option>";
        }
        $selectFields .= "</select>";
        return $selectFields;
    }

    /**
     * @param array $awards
     * @param array $selectFields
     * @return string
     */
    public static function resultsTableView($awards, $selectFields)
    {
        if (empty($awards)) {
            return '<div class="alert alert-info">No awards given for the specified filters</div>';
        }

        // initiate table
        $html = "<table class='table table-hover'>";

        // add the header row
        $html .= '<thead><tr>';
        foreach ($selectFields as $columnKey) {
            $html .= "<th>" . self::$fields[$columnKey]['label'] . "</th>";
        }
        $html .= '</tr></thead>';

        // add the body rows
        $html .= '<tbody>';
        foreach ($awards as $award) {
            $html .= '<tr>';
            foreach ($selectFields as $columnKey) {
                $html .= "<td>" . $award[$columnKey] . "</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';

        // close and return the table
        $html .= '</table>';
        return $html;
    }

    /**
     * @param $awards
     * @return string
     */
    public static function groupByTableView($awards)
    {
        if (empty($awards)) {
            return '<div class="alert alert-info">No awards given for the specified filters</div>';
        }

        // initiate table
        $html = "<table id='datatable' class='table table-hover hidden'>";

        // add the header row
        $html .= '
            <thead>
                <tr>
                    <th></th>
                    <th>Award Count</th>
                </tr>
            </thead>';

        // add the body rows
        $html .= '<tbody>';
        foreach ($awards as $award) {
            $html .= "
                <tr>
                    <td>{$award['label']}</td>
                    <td>{$award['count']}</td>
                </tr>
            ";
        }
        $html .= '</tbody>';

        // close and return the table
        $html .= '</table>';

        return $html;
    }
}