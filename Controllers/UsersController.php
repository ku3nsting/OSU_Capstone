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
            case 'edit-user-form':
                return self::editUserForm($request);
                break;

            case 'edit-user':
                return self::editUser($request);
                break;

            case 'add-user-form':
                return self::addUserForm();
                break;

            case 'add-user':
                return self::addUser($request);
                break;

            case 'delete-user':
                return self::deleteUser($request);

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
        return UsersView::userForm();
    }


    /**
     * @param array $request
     * @return string
     */
    private function editUserForm($request)
    {
        // validate requested user id
        $userId = $this->validateUserId($request['userId']);
        if (empty($userId)) {
            return $this->respondWithErrors(['Invalid user id'], 422);
        }

        // get user and validate user exists
        $user = UsersModel::getUser($userId)[0];

        // return user with form
        return UsersView::userForm($user);
    }

    /**
     * @param array $request
     * @return string
     */
    private function editUser($request)
    {
        // validate requested user id
        $userId = $this->validateUserId($request['userId']);
        if (empty($userId)) {
            return $this->respondWithErrors(['Invalid user id'], 422);
        }

        $formErrors = $this->validateUserFields($request);
        if (!empty($formErrors)) {
            return $this->respondWithErrors($formErrors, 422);
        }

        if (UsersModel::updateUser($request)) {
            return '<div class="alert alert-success">Successfully updated the employee</div>';
        } else {
            http_response_code(500);
            return '<div class="alert alert-danger">Failed to add the employee</div>';
        }
    }

    /**
     * @param $userId
     * @return bool|int
     */
    private function validateUserId($userId)
    {
        // validate requested user id
        $userId = filter_var($userId, FILTER_VALIDATE_INT);
        if (empty($userId)) {
            return false;
        }

        // get user and validate user exists
        $user = UsersModel::getUser($userId)[0];
        if (empty($user['ID'])) {
            return false;
        }

        return $userId;
    }

    /**
     * @param array $request
     * @return string
     */
    private function addUser($request)
    {
        $formErrors = $this->validateUserFields($request);
        if (!empty($formErrors)) {
            return $this->respondWithErrors($formErrors, 422);
        }

        $request['Password'] = password_hash($request['Password'], PASSWORD_DEFAULT);

        $userId = UsersModel::addUser($request);
        if ($userId) {
            $response = [
                'msg' => '<div class="alert alert-success">Successfully added the employee</div>',
                'userId' => $userId
            ];
            return json_encode($response);
        } else {
            http_response_code(500);
            return '<div class="alert alert-danger">Failed to add the employee</div>';
        }
    }

    /**
     * @param $request
     * @return string
     */
    private function deleteUser($request)
    {
        // validate requested user id
        $userId = $this->validateUserId($request['userId']);
        if (empty($userId)) {
            return $this->respondWithErrors(['Invalid user id'], 422);
        }

        if (!UsersModel::deleteUser($userId)) {
            return $this->respondWithErrors(['Could not delete user'], 400);
        }

        return '<div class="alert alert-success">Successfully deleted user</div>';
    }

    /**
     * @param array $request
     * @param bool $passwordCheck
     * @return array
     */
    private function validateUserFields(&$request, $passwordCheck = true)
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
        } else if (!filter_var(($request['Email']), FILTER_VALIDATE_EMAIL)) {
            $formErrors[] = 'Invalid email format';
        }

        $hireDateTime = strtotime($request['hireDate']);
        if (empty($request['hireDate'])) {
            $formErrors[] = 'Hire Date cannot be empty';
        } else if (!empty($request['hireDate']) && !strtotime($request['hireDate'])) {
            $formErrors[] = 'Invalid date format for Hire Date';
        }
        $request['hireDate'] = date('Y-m-d', $hireDateTime);

        if ($passwordCheck && (empty($request['Password']) || strlen($request['Password'])) < 8) {
            $formErrors[] = 'Password must be 8 characters';
        }

        if (empty($request['Type']) || !in_array($request['Type'], ['admin', 'user'])) {
            $formErrors[] = 'User Type must be Admin or Normal User';
        }

        return $formErrors;
    }
}