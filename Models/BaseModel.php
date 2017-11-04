<?php
/**
 * User: jmueller
 * Date: 28/10/17
 * Time: 5:13 PM
 */

namespace models;


use Exception;
use mysqli;

class BaseModel
{
    /**
     * @return mysqli
     * @throws Exception
     */
    public static function getConnection()
    {
        global $dbname, $dbservername, $dbpassword, $dbusername;

        $mysqli = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

        if ($mysqli->connect_errno) {
            $errorMsg = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            throw new Exception($errorMsg);
        }

        return $mysqli;
    }

    /**
     * Generic method to run query and generate error messages
     *
     * @param $query
     * @param array $bindParams
     * @param string $type  fetch_all|insert_get_id
     * @return array|int
     * @throws Exception
     */
    public static function runQuery($query, $bindParams = [], $type = 'fetch_all')
    {
        $mysqli = self::getConnection();

        $stmt = $mysqli->prepare($query);
        if (!$stmt) {
            $errorMsg = "Prepare failed: ({$mysqli->errno}) {$mysqli->error}";
            throw new Exception($errorMsg);
        }

        if (!empty($bindParams)) {
            // The following link was very helpful for this
            // https://stackoverflow.com/questions/1913899/mysqli-binding-params-using-call-user-func-array
            $tmp = [];
            foreach ($bindParams as $key => $value) {
                $tmp[$key] = &$bindParams[$key];
            }
            // Here we are calling $stmt->bind_param($types, $value1, $value2, ...);
            // $tmp has the $types, $value1, $value2 in order in it's array
            if (!call_user_func_array([$stmt, 'bind_param'], $tmp)) {
                $errorMsg = "Bind failed: {$stmt->errno} {$stmt->error}";
                throw new Exception($errorMsg);
            }
        }

        if(!$stmt->execute()) {
            $errorMsg = "Execute failed: {$stmt->errno} {$stmt->error}";
            throw new Exception($errorMsg);
        }

        switch ($type) {
            case 'update':
                return true;
            case 'insert_get_id':
                return $mysqli->insert_id;
            case 'fetch_all':
            default:
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
    }
}