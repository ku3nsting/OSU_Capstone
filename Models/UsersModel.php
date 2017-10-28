<?php
/**
 * User: jmueller
 * Date: 7/10/17
 * Time: 4:54 PM
 */

namespace models;

use Exception;

require_once __DIR__ . '/BaseModel.php';

class UsersModel extends BaseModel
{
    /**
     * @return array
     * @throws Exception
     */
    public static function getUsers()
    {
        $mysqli = self::getConnection();

        $query = "SELECT e.ID, CONCAT_WS(' ', e.fName, e.lName) fullName, e.hireDate, t.Type
            FROM Employees e
            JOIN UserType t ON e.ID = t.EmployeeID";

        $employeeStmt = $mysqli->prepare($query);
        if (!$employeeStmt) {
            $errorMsg = "Prepare failed: ({$mysqli->errno}) {$mysqli->error}";
            throw new Exception($errorMsg);
        }

        if(!$employeeStmt->execute()) {
            $errorMsg = "Execute failed: {$employeeStmt->errno} {$employeeStmt->error}";
            throw new Exception($errorMsg);
        }

        return $employeeStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}