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
     * Get all the users
     *
     * @return array
     * @throws Exception
     */
    public static function getUsers()
    {
        $query = "SELECT e.ID, CONCAT_WS(' ', e.fName, e.lName) fullName, e.hireDate, t.Type
            FROM Employees e
            JOIN UserType t ON e.ID = t.EmployeeID";

        return self::runQuery($query);
    }

    /**
     * Get a user by id
     *
     * @param int $userId
     * @return array
     */
    public static function getUser($userId)
    {
        $query = "
            SELECT e.ID, e.fName, e.lName, e.hireDate, e.Email, t.Type,
             (SELECT COUNT(Awards_Given.ID) FROM Awards_Given WHERE Awards_Given.EmployeeID = e.ID) awardCount
            FROM Employees e
            JOIN UserType t ON e.ID = t.EmployeeID
            WHERE e.ID = ?
        ";

        return self::runQuery($query, ['i', $userId]);
    }

    /**
     * Add a user
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public static function addUser($data)
    {
        $firstName = $data['fName'];
        $lastName = $data['lName'];
        $hireDate = $data['hireDate'];
        $email = $data['Email'];
        $password = $data['Password'];
        $type = $data['Type'];

        $query = "
            INSERT INTO Employees (fName, lName, hireDate, Email, Password, CreatedOn)
            VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
        ";

        $employeeId = self::runQuery(
            $query,
            ['sssss', $firstName, $lastName, $hireDate, $email, $password],
            'insert_get_id'
        );

        $query = "
            INSERT INTO UserType (EmployeeID, Type)
            VALUES (?, ?)
        ";

        self::runQuery($query, ['is', $employeeId, $type], 'insert_get_id');

        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function updateUser($data)
    {
        $employeeId = $data['userId'];
        $firstName = $data['fName'];
        $lastName = $data['lName'];
        $hireDate = $data['hireDate'];
        $email = $data['Email'];
        $type = $data['Type'];

        $query = "UPDATE Employees SET fName = ?, lName = ?, hireDate = ?, Email = ? WHERE ID = ?";
        self::runQuery($query, ['ssssi', $firstName, $lastName, $hireDate, $email, $employeeId], 'update');

        $query = "UPDATE UserType SET UserType.Type = ? WHERE EmployeeID = ?";
        self::runQuery($query, ['si', $type, $employeeId], 'update');

        return true;
    }
}