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

            case 'edit-user':
                return self::editUser($request);

            case 'add-user-form':
                return self::addUserForm();

            case 'add-user':
                return self::addUser($request);

            case 'delete-user':
                return self::deleteUser($request);

            case 'delete-signature':
                return self::deleteUserSignature($request);

            case 'index':
            default:
                return self::index();
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
                'msg' => BaseTemplateView::alert('alert-success', 'Successfully added the employee'),
                'userId' => $userId
            ];
            return json_encode($response);
        } else {
            http_response_code(500);
            return '<div class="alert alert-danger">Failed to add the employee</div>';
        }
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

        $user = $this->getUser($userId);

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
            // Set the messages
            $msg = $this->storeUserSignature($request);
            $msg = BaseTemplateView::alert('alert-success', 'Successfully updated the employee') . $msg;

            // Get the user for the form
            $user = $this->getUser($userId);

            // return the messages and form
            return json_encode(['msg' => $msg, 'userForm' => UsersView::userForm($user)]);
        } else {
            http_response_code(500);
            return '<div class="alert alert-danger">Failed to add the employee</div>';
        }
    }

    /**
     * @param int $userId
     * @return array
     */
    private function getUser($userId)
    {
        // get user and validate user exists
        $user = UsersModel::getUser($userId)[0];

        if (file_exists($this->getUserSignFile($userId))) {
            $user['signFile'] = $this->getUserSignFile($userId, 'src');
        }

        return $user;
    }

    /**
     * Store the user's signature file
     *
     * @param $request
     * @return string
     */
    private function storeUserSignature($request)
    {
        if (empty($_FILES['signature']['tmp_name'])) {
            return '';
        }

        if (move_uploaded_file($_FILES['signature']['tmp_name'], $this->getUserSignFile($request['userId']))) {
            return BaseTemplateView::alert('alert-success', "Successfully stored the signature file");
        }

        return BaseTemplateView::alert(
            'alert-danger',
            'Failed to store the signature file. Please try again. If the problem persists please contact your site administrator'
        );
    }

    /**
     * @param $request
     * @return string
     */
    private function deleteUserSignature($request)
    {
        $fileName = $this->getUserSignFile($request['userId']);

        if (file_exists($fileName)) {
            unlink($fileName);
        }

        return UsersView::userSignatureFormField();
    }

    /**
     * @param $userId
     * @param string $type
     * @return string
     */
    private function getUserSignFile($userId, $type = 'full-path')
    {
        $fileLocation = '/uploads/signatureEmployeeId' . $userId;

        if ($type === 'full-path') {
            return $_SERVER['DOCUMENT_ROOT'] . $fileLocation;
        }

        return $fileLocation;
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

        $this->deleteUserSignature($request);

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

        if ($passwordCheck && $request['Password'] !== $request['PasswordAgain']) {
            $formErrors[] = 'Passwords do not match';
        }

        if (empty($request['Type']) || !in_array($request['Type'], ['admin', 'user'])) {
            $formErrors[] = 'User Type must be Admin or Normal User';
        }

        return $formErrors;
    }
}