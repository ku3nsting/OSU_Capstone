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
            <div class="col-sm-2 pull-right">
                <a href="#" id="addUserFormBtn" class="btn btn-primary" style="margin-top: 20px">Add User Form</a>
            </div>
        </div>
        <div id="msg-div"></div>
        <div id="manage-users-content">
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
                        <th>Hire Date (YYYY-MM-DD)</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>$userRows</tbody>
            </table>
        ";
    }

    public static function addUserForm()
    {
        return '
            <form id="add-user-form">
                <input class="hidden" id="action" name="action" value="add-user">
                <div class="form-group">
                     <label for="fName">First Name</label>           
                     <input type="text" id="fName" name="fName" class="form-control">
                </div>
                <div class="form-group">
                     <label for="lName">Last Name</label>           
                     <input type="text" id="lName" name="lName" class="form-control">
                </div>
                <div class="form-group">
                     <label for="Email">Email</label>           
                     <input type="email" id="Email" name="Email" class="form-control">
                </div>
                <div class="form-group">
                     <label for="hireDate">Hire Date</label>           
                     <input type="date" id="hireDate" name="hireDate" class="form-control">
                </div>
                <div class="form-group">
                     <label for="Password">Password</label>
                     <input type="password" id="Password" name="Password" class="form-control">
                </div>
                <div class="form-group">
                     <label for="Type">Type</label>           
                     <select id="Type" name="Type" class="form-control">
                        <option value="user">Normal User</option>
                        <option value="admin">Admin User</option>
                     </select>
                </div>
                <a class="btn btn-primary" href="#" role="button" id="addUserBtn">Submit</a>
            </form>
        ';
    }
}