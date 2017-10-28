<?php
/**
 * User: jmueller
 * Date: 28/10/17
 * Time: 2:40 PM
 */

namespace controllers;


use models\UsersModel;
use views\BaseTemplateView;
use views\UsersView;

require_once 'BaseController.php';
require_once __DIR__ . '/../Views/BaseTemplateView.php';
require_once __DIR__ . '/../Views/UsersView.php';
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Models/UsersModel.php';

class UsersController extends BaseController
{
    /**
     * @param $request
     * @return string
     */
    public function respond($request)
    {
        switch ($request['action']) {
            case 'add-user-form':
                return self::addUserForm();
            case 'add-user':
                return self::addUser($request);
            case 'index':
            default:
                return self::index();
                break;
        }
    }

    /**
     * Return the Admin Reports Page
     * @return string
     * @throws \Exception
     */
    private function index()
    {
        $users = UsersModel::getUsers();

        // make necessary queries calls through models
        // return views related to the initial reports landing page
        return BaseTemplateView::baseTemplateView(
            'admin',
            UsersView::indexView($users),
            'manageUsers.init();'
        );
    }

    /**
     * @return string
     */
    private function addUserForm()
    {
        return UsersView::addUserForm();
    }

    private function addUser($request)
    {
        $formErrors = $this->validateUserFields($request);
        if (!empty($formErrors)) {
            echo '<pre>';
            var_dump($formErrors);
            echo '</pre>';
        }
    }

    private function validateUserFields($request)
    {
        $formErrors = [];

        if (empty($request['fName'])) {
            $formErrors[] = 'First Name cannot be empty';
        }

        if (empty($request['lName'])) {
            $formErrors[] = 'Last Name cannot be empty';
        }

        if (empty($request['Email'])) {
            $formErrors[] = 'Email cannot be empty';
        }

        if (filter_var(($request['Email']), FILTER_VALIDATE_EMAIL)) {
            $formErrors[] = 'Invalid email format';
        }

        if (empty($request['hireDate'])) {
            $formErrors[] = 'Hire Date cannot be empty';
        }

        if (!empty($request['hireDate']) && !strtotime($request['hireDate'])) {
            $formErrors[] = 'Invalid date format for Hire Date';
        }

        if (empty($request['Password']) || strlen($request['Password']) < 8) {
            $formErrors[] = 'Password must be 8 characters';
        }

        if (empty($request['Type']) || !in_array($request['Type'], ['admin', 'user'])) {
            $formErrors[] = 'User Type must be Admin or Normal User';
        }

        return $formErrors;
    }
}