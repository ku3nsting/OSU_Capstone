<?php
/**
 * User: jmueller
 * Date: 7/10/17
 * Time: 4:54 PM
 */

namespace views;


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
        return '
            <form id="selectQueryForm" action="/admin/reports.php">
                <h2>Report Query Builder</h2>
                <input type="hidden" value="run-query" id="action" name="action">
                <input type="hidden" value="" id="rules" name="rules">
                <input type="hidden" value="" id="csvExport" name="csvExport">
                <div class="form-group">
                    <label for="selectQueryFields">SELECT Fields</label>
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true" style="cursor: help;" title="To select multiple click and drag mouse or hold Control and Click with mouse"></span>
                    ' . self::selectQueryFields() . '
                </div>
                <label>WHERE</label>
                <div id="builder"></div>
                <a class="btn btn-primary btn-sm" href="#" role="button" onclick="report.runQuery();">Submit</a>
                <a class="btn btn-primary btn-sm" href="#" role="button" onclick="report.exportCsv();">Export to CSV</a>
            </form>
            <h2>Results</h2>
            <div id="query-results"></div>
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

}