<?php
/**
 * User: jmueller
 * Date: 22/10/17
 * Time: 10:25 AM
 */

namespace database;


require_once __DIR__ . '/../../Config/database.php';
use mysqli;

class AwardsQueryBuilder
{
    /**
     * Base query for the query builder
     * @var string
     */
    public static $query = "
        SELECT Awards.AwardLabel, Awards_Given.AwardDate,
          Employees.fName, Employees.lName, Employees.Email, Employees.hireDate,
          Giver.fName GiverFirstName, Giver.lName GiverLastName, Giver.Email GiverEmail
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
            ]
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

    public function runQuery(array $rules)
    {
        $query = $this->buildQuery($rules);

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
     * @return string
     */
    public function buildQuery(array $rules)
    {
        $whereClause = $this->buildWhereClause($rules);

        return !empty($whereClause) ? self::$query . ' AND ' . $whereClause : self::$query;
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
            throw new \Exception("Field, $field, does not exist");
        }

        // Validate the type passed through is valid
        if ($this->fields[$field]['type'] !== $type) {
            throw new \Exception("Invalid type, $type, with field, $field");
        }

        // Validate operator
        if (!array_key_exists($operator, $this->operators[$type])) {
            throw new \Exception("Invalid operator, $operator, for field, $field, of type, $type");
        }

        // TODO: validate value (only for date)
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

    //TODO: build select clause
}