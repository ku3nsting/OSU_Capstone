<?php
/**
 * User: jmueller
 * Date: 22/10/17
 * Time: 10:25 AM
 */

namespace database;


require_once __DIR__ . '/../../Config/database.php';

use DateTime;
use mysqli;

class AwardsQueryBuilder
{
    /**
     * Base query for the query builder
     * @var string
     */
    public static $query = "
        FROM Awards_Given
        JOIN Awards ON Awards_Given.AwardID = Awards.ID
        JOIN Employees ON Awards_Given.AwardedByID = Employees.ID
        JOIN Employees as Giver ON Awards_Given.AwardedByID = Giver.ID
        JOIN UserType ON Employees.ID = UserType.EmployeeID
        WHERE UserType.Type = 'user'
    ";

    private $mysqli = null;

    /**
     * Variable to store the query values (for prepared queries)
     * @var array
     */
    private $values = [];

    /**
     * Variable to store the query value types (for prepared queries)
     * @var array
     */
    private $valueTypes = [];

    /**
     * Definition of available query fields and traits
     * @var array
     */
    private $fields = [
        "AwardLabel" => [
            "dbfield" => "Awards.AwardLabel",
            "type" => "string",
        ],
        "AwardDate" => [
            "dbfield" => "Awards_Given.AwardDate",
            "type" => "date",
        ],
        "Email" => [
            "dbfield" => "Employees.Email",
            "type" => "string",
        ],
        "fName" => [
            "dbfield" => "Employees.fName",
            "type" => "string",
        ],
        "lName" => [
            "dbfield" => "Employees.lName",
            "type" => "string",
        ],
        "hireDate" => [
            "dbfield" => "Employees.hireDate",
            "type" => "date",
        ],
        "GiverFirstName" => [
            "dbfield" => "Giver.fName",
            "type" => "string",
        ],
        "GiverLastName" => [
            "dbfield" => "Giver.lName",
            "type" => "string",
        ],
        "GiverEmail" => [
            "dbfield" => "Giver.Email",
            "type" => "string",
        ],
    ];

    public static $groupByFields = [
        "month" => [
            "dbfield" => "month",
            "groupby" => "monthIndex, month",
            "orderby" => "monthIndex",
            "option-label" => "Award Month",
        ],
        "year" => [
            "dbfield" => "year",
            "groupby" => "year",
            "orderby" => "year",
            "option-label" => "Award Year",
        ],
        "month-year" => [
            "dbfield" => "CONCAT(month, ' ', year)",
            "groupby" => "monthIndex, month, year",
            "orderby" => "year, monthIndex",
            "option-label" => "Award Month and Year",
        ],
        "award-type" => [
            "dbfield" => "AwardLabel",
            "groupby" => "AwardLabel",
            "orderby" => "AwardLabel",
            "option-label" => "Award Type",
        ],
        "award-date" => [
            "dbfield" => "AwardDate",
            "groupby" => "AwardDate",
            "orderby" => "AwardDate",
            "option-label" => "Award Date",
        ],
        "awardee-email" => [
            "dbfield" => "Email",
            "groupby" => "Email",
            "orderby" => "Email",
            "option-label" => "Awardee Email",
        ],
        "awardee" => [
            "dbfield" => "CONCAT(fName, ' ', lName)",
            "groupby" => "EmployeeId, fName, lName",
            "orderby" => "lName",
            "option-label" => "Awardee",
        ],
        "awardee-hire-date" => [
            "dbfield" => "hireDate",
            "groupby" => "hireDate",
            "orderby" => "hireDate",
            "option-label" => "Awardee Hire Date",
        ],
        "giver-email" => [
            "dbfield" => "GiverEmail",
            "groupby" => "GiverEmail",
            "orderby" => "GiverEmail",
            "option-label" => "Giver Email",
        ],
        "giver" => [
            "dbfield" => "CONCAT(GiverFirstName, ' ', GiverLastName)",
            "groupby" => "GiverEmployeeId, GiverFirstName, GiverLastName",
            "orderby" => "GiverLastName",
            "option-label" => "Giver",
        ],
    ];

    /**
     * Definition of available operators and their traits
     * @var array
     */
    private $operators = [
        'date' => [
            'equal' => [
                'sqlOperator' => '=',
                'valuePrepend' => '',
                'valueAppend' => '',
            ],
            'not_equal' => [
                'sqlOperator' => '!=',
                'valuePrepend' => '',
                'valueAppend' => '',
            ],
            'greater' => [
                'sqlOperator' => '>',
                'valuePrepend' => '',
                'valueAppend' => '',
            ],
            'greater_or_equal' => [
                'sqlOperator' => '>=',
                'valuePrepend' => '',
                'valueAppend' => '',
            ],
            'less' => [
                'sqlOperator' => '<',
                'valuePrepend' => '',
                'valueAppend' => '',
            ],
            'less_or_equal' => [
                'sqlOperator' => '<=',
                'valuePrepend' => '',
                'valueAppend' => '',
            ],
        ],
        'string' => [
            'equal' => [
                'sqlOperator' => '=',
                'valuePrepend' => '',
                'valueAppend' => '',
            ],
            'not_equal' => [
                'sqlOperator' => '!=',
                'valuePrepend' => '',
                'valueAppend' => '',
            ],
            'begins_with' => [
                'sqlOperator' => 'LIKE',
                'valuePrepend' => '',
                'valueAppend' => '%',
            ],
            'not_begins_with' => [
                'sqlOperator' => 'NOT LIKE',
                'valuePrepend' => '',
                'valueAppend' => '%',
            ],
            'contains' => [
                'sqlOperator' => 'LIKE',
                'valuePrepend' => '%',
                'valueAppend' => '%',
            ],
            'not_contains' => [
                'sqlOperator' => 'NOT LIKE',
                'valuePrepend' => '%',
                'valueAppend' => '%',
            ],
            'ends_with' => [
                'sqlOperator' => 'LIKE',
                'valuePrepend' => '%',
                'valueAppend' => '',
            ],
            'not_ends_with' => [
                'sqlOperator' => 'NOT LIKE',
                'valuePrepend' => '%',
                'valueAppend' => '',
            ],
        ]
    ];

    /**
     * AwardsQueryBuilder constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        global $dbname, $dbservername, $dbpassword, $dbusername;

        $this->mysqli = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

        if ($this->mysqli->connect_error) {
            throw new \Exception('Connection failed: ' . $this->mysqli->connect_error);
        }
    }

    /**
     * Returns the values array
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return array
     */
    public function getValueTypes()
    {
        return $this->valueTypes;
    }

    /**
     * Gets the parameters for binding to sql statement for use with call_user_func
     * @return array
     */
    public function getBindParams()
    {
        $values = $this->values;
        array_unshift($values, implode('', $this->valueTypes));
        return $values;
    }

    /**
     * @param array $rules
     * @param array $selectFields
     * @param array $groupBy
     * @return array|null
     * @throws \Exception
     */
    public function runQuery(array $rules, array $selectFields, $groupBy = [])
    {
        $query = $this->buildQuery($rules, $selectFields, $groupBy);

        $stmt = $this->mysqli->prepare($query);

        $params = $this->getBindParams();
        // The following link was very helpful for this
        // https://stackoverflow.com/questions/1913899/mysqli-binding-params-using-call-user-func-array
        $tmp = [];
        foreach ($params as $key => $value) $tmp[$key] = &$params[$key];
        // Here we are calling $stmt->bind_param($types, $value1, $value2, ...);
        // $tmp has the $types, $value1, $value2 in order in it's array
        call_user_func_array([$stmt, 'bind_param'], $tmp);

        if(!$stmt->execute()) {
            throw new \Exception('Invalid Query');
        }

        $awardResult = $stmt->get_result();
        $awards = $awardResult->fetch_all(MYSQLI_ASSOC);

        return $awards;
    }

    /**
     * @param array $rules
     * @param array $selectFields
     * @param array $groupBy
     * @return string
     * @throws \Exception
     */
    public function buildQuery(array $rules, array $selectFields, $groupBy = [])
    {
        if (!empty($groupBy)) {
            $select = $this->getGroupBySelectSql();
        } else {
            $select = $this->getSelectSql($selectFields);
        }

        $whereClause = $this->buildWhereClause($rules);

        if(empty($whereClause) || empty($select)) {
            throw new \Exception('Missing select or where clause');
        }
        $query = $select . self::$query . ' AND ' . $whereClause;

        if (!empty($groupBy)) {
            $query = $this->addGroupBy($query, $groupBy);
        }

        return $query;
    }

    /**
     * @param array $rules
     * @return string
     */
    private function buildWhereClause($rules)
    {
        return $this->addGroup($rules);
    }

    /**
     * @param $rules
     * @return string
     */
    private function addGroup($rules)
    {
        $condition = ' ' . $rules['condition'] . ' ';
        $ruleArray = [];

        foreach ($rules['rules'] as $rule) {
            $ruleArray[] = $this->addRule($rule);
        }

        return '(' . implode($condition, $ruleArray) . ")";
    }

    /**
     * @param $rule
     * @return string
     */
    private function addRule($rule)
    {
        if (array_key_exists('condition', $rule)) {
            return $this->addGroup($rule);
        }

        $this->validateField($rule);

        $this->values[] = $this->getValue($rule);
        $this->valueTypes[] = 's';

        return implode(' ', [
            $this->getField($rule),
            $this->getOperator($rule),
            '?'
        ]);
    }

    /**
     * Validates the rule before generate SQL
     * @param $rule
     * @throws \Exception
     */
    private function validateField($rule)
    {
        $type = $rule['type'];
        $operator = $rule['operator'];
        $field = $rule['field'];

        // Validate the field is a valid filter field
        if (!array_key_exists($field, $this->fields)) {
            throw new \Exception("Field, $field, does not exist", 422);
        }

        // Validate the type passed through is valid
        if ($this->fields[$field]['type'] !== $type) {
            throw new \Exception("Invalid type, $type, with field, $field", 422);
        }

        // Validate operator
        if (!array_key_exists($operator, $this->operators[$type])) {
            throw new \Exception("Invalid operator, $operator, for field, $field, of type, $type", 422);
        }

        if ($this->fields[$field]['type'] === 'date') {
            $date = DateTime::createFromFormat('Y-m-d', $rule['value']);
            // PHP allows overflow on the day when creating a date. So if March 32nd is given it will generate April 1
            // so we check if the date is empty/false or if when the created date is formatted it matches it's original string representation
            if (empty($date) || $date->format('Y-m-d') !== $rule['value']) {
                throw new \Exception(
                    "Invalid date, {$rule['value']}, given for field, $field. 'Y-m-d' is the valid date format.",
                    422
                );
            }
        }
    }

    /**
     * @param $rule
     * @return mixed
     * @throws \Exception
     */
    private function getField($rule)
    {
        $field = $rule['field'];

        return $this->fields[$field]['dbfield'];
    }

    /**
     * @param $rule
     * @return mixed
     */
    private function getOperator($rule)
    {
        $type = $rule['type'];
        $operator = $rule['operator'];

        return $this->operators[$type][$operator]['sqlOperator'];
    }

    /**
     * @param $rule
     * @return string
     */
    private function getValue($rule)
    {
        $type = $rule['type'];
        $operator = $rule['operator'];

        return $this->operators[$type][$operator]['valuePrepend']
            . $rule['value']
            . $this->operators[$type][$operator]['valueAppend'];
    }

    /**
     * @param $selectFields
     * @return string
     * @throws \Exception
     */
    private function getSelectSql($selectFields)
    {
        if (empty($selectFields)) {
            throw new \Exception('No Select Fields were chosen for the Query', 422);
        }

        $selectDbFields = [];
        foreach ($selectFields as $selectField) {
            if (!array_key_exists($selectField, $this->fields)) {
                throw new \Exception("Field, $selectField, is not a valid select field", 422);
            }
            $selectDbFields[] = $this->fields[$selectField]['dbfield'] . " AS $selectField";
        }
        return 'SELECT ' . implode(', ', $selectDbFields) . ' ';
    }

    /**
     * @return string
     */
    private function getGroupBySelectSql()
    {
        $select = $this->getSelectSql(array_keys($this->fields));
        $select .= ', 
            MONTHNAME(Awards_Given.AwardDate) as month, 
            MONTH(Awards_Given.AwardDate) as monthIndex,
            YEAR(Awards_Given.AwardDate) as year,
            Employees.ID as EmployeeId,
            Giver.ID as GiverEmployeeId';
        return $select;
    }

    /**
     * @param string $query
     * @param array $groupBy
     * @return string
     */
    private function addGroupBy($query, array $groupBy)
    {
        $groupByField = self::$groupByFields[$groupBy['group-by-1']];

        $query = "SELECT {$groupByField['dbfield']} as label, COUNT(*) as `count`
            FROM ($query) AS subquery
            GROUP BY {$groupByField['groupby']}
            ORDER BY {$groupByField['orderby']}";

        return $query;
    }
}