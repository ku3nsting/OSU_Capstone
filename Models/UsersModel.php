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

    public static function getUser($userId)
    {
        $mysqli = self::getConnection();

        $query = "
            SELECT e.ID, e.fName, e.lName, e.hireDate, e.Email, t.Type
            FROM Employees e
            JOIN UserType t ON e.ID = t.EmployeeID
            WHERE e.ID = ?
        ";

        $employeeStmt = $mysqli->prepare($query);
        if (!$employeeStmt) {
            $errorMsg = "Prepare failed: ({$mysqli->errno}) {$mysqli->error}";
            throw new Exception($errorMsg);
        }

        if (!$employeeStmt->bind_param('i', $userId)) {
            $errorMsg = "Bind failed: {$employeeStmt->errno} {$employeeStmt->error}";
            throw new Exception($errorMsg);
        }

        if(!$employeeStmt->execute()) {
            $errorMsg = "Execute failed: {$employeeStmt->errno} {$employeeStmt->error}";
            throw new Exception($errorMsg);
        }

        return $employeeStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function addUser($data)
    {
        $firstName = $data['fName'];
        $lastName = $data['lName'];
        $hireDate = $data['hireDate'];
        $email = $data['Email'];
        $password = $data['Password'];
        $type = $data['Type'];

        $mysqli = self::getConnection();

        $query = "
            INSERT INTO Employees (fName, lName, hireDate, Email, Password, CreatedOn)
            VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ";

        $employeeStmt = $mysqli->prepare($query);
        if (!$employeeStmt) {
            $errorMsg = "Prepare failed: ({$mysqli->errno}) {$mysqli->error}";
            throw new Exception($errorMsg);
        }

        if (!$employeeStmt->bind_param('sssss', $firstName, $lastName, $hireDate, $email, $password)) {
            $errorMsg = "Bind failed: {$employeeStmt->errno} {$employeeStmt->error}";
            throw new Exception($errorMsg);
        }

        if(!$employeeStmt->execute()) {
            $errorMsg = "Execute failed: {$employeeStmt->errno} {$employeeStmt->error}";
            throw new Exception($errorMsg);
        }
        // Get the employee ID for the UserType addition
        $employeeId = $mysqli->insert_id;

        $query = "
            INSERT INTO UserType (EmployeeID, Type)
            VALUES (?, ?)
        ";

        $userTypeStmt = $mysqli->prepare($query);
        if (!$userTypeStmt) {
            $errorMsg = "Prepare failed: ({$mysqli->errno}) {$mysqli->error}";
            throw new Exception($errorMsg);
        }

        if (!$userTypeStmt->bind_param('is', $employeeId, $type)) {
            $errorMsg = "Bind failed: {$userTypeStmt->errno} {$userTypeStmt->error}";
            throw new Exception($errorMsg);
        }

        if(!$userTypeStmt->execute()) {
            $errorMsg = "Execute failed: {$userTypeStmt->errno} {$userTypeStmt->error}";
            throw new Exception($errorMsg);
        }

        return true;
    }
}