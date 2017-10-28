<?php
/**
 * User: jmueller
 * Date: 28/10/17
 * Time: 2:44 PM
 */

namespace views;


class UsersView
{
    /**
     * @return string
     */
    public static function indexView($users)
    {
        $userTable = self::usersTable($users);

        return '<div class="row">
            <h1 class="col-sm-9">Manage Users</h1>
            <div class="col-sm-1 pull-right">
                <a href="#" class="btn btn-primary" style="margin-top: 20px">Add User</a>
            </div>
        </div>
        <div class="row">
            ' . $userTable . '
        </div>
        ';
    }

    /**
     * @param $users
     * @return string
     */
    public static function usersTable($users)
    {
        if (empty($users)) {
            return "<div class='alert alert-info'>No users in database</div>";
        }

        // create the users tables rows
        $userRows = '';
        foreach ($users as $user) {
            $userRows .= "<tr>
                <td>{$user['ID']}</td>
                <td>{$user['fullName']}</td>
                <td>{$user['hireDate']}</td>
                <td>" . ucfirst($user['Type']) . "</td>
            </tr>";
        }

        // create the users table
        return "
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th>User Id</th>
                        <th>Employee Name</th>
                        <th>Hire Date</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>$userRows</tbody>
            </table>
        ";
    }
}