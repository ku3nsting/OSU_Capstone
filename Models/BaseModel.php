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
}