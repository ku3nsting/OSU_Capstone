<?php
/**
 * User: jmueller
 * Date: 7/10/17
 * Time: 4:54 PM
 */

namespace views;


class ReportsViews
{
    private static $fields = [
        [
            "id" => "AwardLabel",
            "label" => "Award Type",
            "type" => "string",
        ],
        [
            "id" => "AwardDate",
            "label" => "Award Date",
            "type" => "date",
        ],
        [
            "id" => "Email",
            "label" => "Awardee Email",
            "type" => "string",
        ],
        [
            "id" => "fName",
            "label" => "Awardee First Name",
            "type" => "string",
        ],
        [
            "id" => "lName",
            "label" => "Awardee Last Name",
            "type" => "string",
        ],
        [
            "id" => "hireDate",
            "label" => "Awardee Hire Date",
            "type" => "date",
        ],
        [
            "id" => "GiverFirstName",
            "label" => "Giver First Name",
            "type" => "string",
        ],
        [
            "id" => "GiverLastName",
            "label" => "Giver Last Name",
            "type" => "string",
        ],
        [
            "id" => "GiverEmail",
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
                <div class="form-group">
                    <label for="selectQueryFields">SELECT Fields</label>
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true" style="cursor: help;" title="To select multiple click and drag mouse or hold Control and Click with mouse"></span>
                    ' . self::selectQueryFields() . '
                </div>
                <label>WHERE</label>
                <div id="builder"></div>
                <a class="btn btn-primary" href="#" role="button" onclick="report.runQuery();">Submit</a>
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
        foreach (self::$fields as $field) {
            $selectFields .= "<option value='{$field['id']}'>{$field['label']}</option>";
        }
        $selectFields .= "</select>";
        return $selectFields;
    }

}