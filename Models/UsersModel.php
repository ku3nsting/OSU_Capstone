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
     * @param $offset
     * @return array
     */
    public static function getUsers($offset)
    {
        $query = "SELECT e.ID, CONCAT_WS(' ', e.fName, e.lName) fullName, e.hireDate, t.Type
            FROM Employees e
            JOIN UserType t ON e.ID = t.EmployeeID
            ORDER BY t.Type, e.lName, e.ID
            LIMIT 15
            OFFSET $offset
            ";

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
            SELECT e.ID, e.fName, e.lName, e.hireDate, e.Email, t.Type, e.Bio,
             (SELECT COUNT(Awards_Given.ID) FROM Awards_Given WHERE Awards_Given.EmployeeID = e.ID) awardCount
            FROM Employees e
            JOIN UserType t ON e.ID = t.EmployeeID
            WHERE e.ID = ?
        ";

        return self::runQuery($query, ['i', $userId]);
    }

    /**
     * Returns a user count
     * @return array
     */
    public static function userCount()
    {
        $query = "SELECT COUNT(e.ID) as userCount
            FROM Employees e
            JOIN UserType t ON e.ID = t.EmployeeID";

        return self::runQuery($query);
    }

    /**
     * Get a user by id
     *
     * @param string $email
     * @return array
     */
    public static function getAdminUserForAuthentication($email)
    {
        $query = "
            SELECT e.ID, e.Password, e.Email, t.Type
            FROM Employees e
            JOIN UserType t ON e.ID = t.EmployeeID
            WHERE e.Email = ?
            AND t.Type = 'admin'
        ";

        return self::runQuery($query, ['s', $email]);
    }

    /**
     * Add a user
     *
     * @param array $data
     * @return int
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
        $bio = $data['Bio'];

        $query = "
            INSERT INTO Employees (fName, lName, hireDate, Email, Password, CreatedOn, Bio)
            VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?)
        ";

        $employeeId = self::runQuery(
            $query,
            ['ssssss', $firstName, $lastName, $hireDate, $email, $password, $bio],
            'insert_get_id'
        );

        $query = "
            INSERT INTO UserType (EmployeeID, Type)
            VALUES (?, ?)
        ";

        self::runQuery($query, ['is', $employeeId, $type], 'insert_get_id');

        return $employeeId;
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
        $bio = $data['Bio'];

        $query = "UPDATE Employees SET fName = ?, lName = ?, hireDate = ?, Email = ?, Bio = ? WHERE ID = ?";
        self::runQuery($query, ['sssssi', $firstName, $lastName, $hireDate, $email, $bio, $employeeId], 'update');

        $query = "UPDATE UserType SET UserType.Type = ? WHERE EmployeeID = ?";
        self::runQuery($query, ['si', $type, $employeeId], 'update');

        return true;
    }

    /**
     * @param $userId
     * @return boolean
     * @throws Exception
     */
    public static function deleteUser($userId)
    {
        $query = "SELECT COUNT(*) as awardsCount FROM Awards_Given WHERE EmployeeID = ?";
        $awardsCount = self::runQuery($query, ['i', $userId])[0];

        if (!empty($awardsCount['awardsCount'])) {
            throw new Exception('Cannot delete the user because the user is still associated with given awards');
        }

        $query = "DELETE FROM UserType WHERE EmployeeID = ?";
        self::runQuery($query, ['i', $userId], 'update');

        $query = "DELETE FROM Employees WHERE ID = ?";
        self::runQuery($query, ['i', $userId], 'update');

        return true;
    }
}