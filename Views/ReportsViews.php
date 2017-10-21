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
            <form>
                <h2>Report Query Builder</h2>
                <div class="form-group">
                    <label for="selectQueryFields" style="cursor: help;" title="hold ctrl or shift (or drag with the mouse) to select more than one">Select Fields:</label>
                    ' . self::selectQueryFields() . '
                </div>
                <label>Where Clause:</label>
                <div id="builder"></div>
            </form>
        ';
    }

    /**
     * Returns the select query multi select field
     * @return string
     */
    public static function selectQueryFields()
    {
        $selectFields = "<select id='selectQueryFields' multiple class='form-control' style='height: 12.5em'>";
        foreach (self::$fields as $field) {
            $selectFields .= "<option value='{$field['id']}'>{$field['label']}</option>";
        }
        $selectFields .= "</select>";
        return $selectFields;
    }

}