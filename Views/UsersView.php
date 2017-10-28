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
        $usersString = print_r($users, true);

        return '<div class="row">
            <h1 class="col-sm-9">Manage Users</h1>
            <div class="col-sm-1 pull-right">
                <a href="#" class="btn btn-primary" style="margin-top: 20px">Add User</a>
            </div>
        </div>
        <div class="row">
            <pre>' . $usersString . '</pre>
        </div>
        ';
    }
}