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
    public static $signFileTypes = ['image/png' => 'png'];
    public static $profilePhotoTypes = [
        'image/png' => 'png',
        'image/jpeg' => 'jpeg',
        'image/bmp' => 'bmp'
    ];

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

            case 'delete-profile-photo':
                return self::deleteUserProfilePhoto($request);

            case 'page':
                return self::usersTablePage($request);
                break;

            case 'index':
            default:
                return self::index();
        }
    }

    /**
     * Return the Admin Reports Page
     * @return string
     */
    private function index()
    {
        $users = UsersModel::getUsers(0);
        $userCount = UsersModel::userCount()[0]['userCount'];

        // make necessary queries calls through models
        // return views related to the initial reports landing page
        return BaseTemplateView::baseTemplateView(
            'admin',
            UsersView::indexView($users, 0, $userCount),
            'manageUsers.init();'
        );
    }

    /**
     * Returns the page of the users table
     *
     * @param $request
     * @return string
     * @throws \Exception
     */
    private function usersTablePage($request)
    {
        $offset = isset($request['offset']) ? $request['offset'] : 0;
        $offset = filter_var($offset,FILTER_VALIDATE_INT);

        $userCount = UsersModel::userCount()[0]['userCount'];

        if (!is_int($offset) || $offset % 15 !== 0 || $offset >= $userCount) {
            throw new \Exception('Invalid page offset', 422);
        }

        $users = UsersModel::getUsers($offset);

        return UsersView::usersTable($users, $offset, $userCount) . '<script>manageUsers.init();</script>';
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
            $msg .= $this->storeUserProfilePhoto($request);
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

        $fileName = self::getExistingUserSignFile($userId, 'src');
        if (!empty($fileName)) {
            $user['signFile'] = $fileName;
        }

        $fileName = self::getExistingUserProfilePhoto($userId, 'src');
        if (!empty($fileName)) {
            $user['profilePhoto'] = $fileName;
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

        if (move_uploaded_file(
            $_FILES['signature']['tmp_name'],
            self::getUserSignFile($request['userId'], 'full-path', $_FILES['signature']['type'])
        )) {
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
        $fileName = self::getExistingUserSignFile($request['userId']);
        if (!empty($fileName)) {
            unlink($fileName);
        }

        return UsersView::userSignatureFormField();
    }

    /**
     * @param $userId
     * @param string $type
     * @param string $mimeType
     * @return string
     */
    public static function getUserSignFile($userId, $type = 'full-path', $mimeType = '')
    {
        $extension = empty($mimeType) ? '' : '.' . self::$signFileTypes[$mimeType];
        $fileLocation = '/uploads/signatureEmployeeId' . $userId . $extension;

        if ($type === 'full-path') {
            return $_SERVER['DOCUMENT_ROOT'] . $fileLocation;
        }

        return $fileLocation;
    }

    /**
     * Get the existing user signature file name
     *
     * @param $userId
     * @param string $type
     * @return bool|string
     */
    public static function getExistingUserSignFile($userId, $type = 'full-path')
    {
        $fileName = self::getUserSignFile($userId);

        if (file_exists($fileName)) {
            return self::getUserSignFile($userId, $type);
        }

        foreach (self::$signFileTypes as $mimeType => $ext) {
            $fileNameWithExt = "$fileName.$ext";
            if (file_exists($fileNameWithExt)) {
                return self::getUserSignFile($userId, $type, $mimeType);
            }
        }

        return false;
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
        $this->deleteUserProfilePhoto($request);

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

        if (!empty($_FILES['signature']['tmp_name']) &&
            !in_array($_FILES['signature']['type'], array_keys(self::$signFileTypes))
        ) {
            $formErrors[] = 'Employee signature must be one of the following image types: ('
                . implode(',', self::$signFileTypes) . ')';
        }

        return $formErrors;
    }

    /**
     * Store the user's signature file
     *
     * @param $request
     * @return string
     */
    private function storeUserProfilePhoto($request)
    {
        if (empty($_FILES['profilePhoto']['tmp_name'])) {
            return '';
        }

        if (move_uploaded_file(
            $_FILES['profilePhoto']['tmp_name'],
            self::getUserProfilePhoto($request['userId'], 'full-path', $_FILES['profilePhoto']['type'])
        )) {
            return BaseTemplateView::alert('alert-success', "Successfully stored the profile photo");
        }

        return BaseTemplateView::alert(
            'alert-danger',
            'Failed to store the profile photo. Please try again. If the problem persists please contact your site administrator'
        );
    }

    /**
     * @param $request
     * @return string
     */
    private function deleteUserProfilePhoto($request)
    {
        $fileName = self::getExistingUserProfilePhoto($request['userId']);
        if (!empty($fileName)) {
            unlink($fileName);
        }

        return UsersView::userProfilePhotoFormField();
    }

    /**
     * @param $userId
     * @param string $type
     * @param string $mimeType
     * @return string
     */
    public static function getUserProfilePhoto($userId, $type = 'full-path', $mimeType = '')
    {
        $extension = empty($mimeType) ? '' : '.' . self::$profilePhotoTypes[$mimeType];
        $fileLocation = '/uploads/profilePhotoEmployeeId' . $userId . $extension;

        if ($type === 'full-path') {
            return $_SERVER['DOCUMENT_ROOT'] . $fileLocation;
        }

        return $fileLocation;
    }

    /**
     * Get the existing user signature file name
     *
     * @param $userId
     * @param string $type
     * @return bool|string
     */
    public static function getExistingUserProfilePhoto($userId, $type = 'full-path')
    {
        $fileName = self::getUserProfilePhoto($userId);

        if (file_exists($fileName)) {
            return self::getUserProfilePhoto($userId, $type);
        }

        foreach (self::$signFileTypes as $mimeType => $ext) {
            $fileNameWithExt = "$fileName.$ext";
            if (file_exists($fileNameWithExt)) {
                return self::getUserProfilePhoto($userId, $type, $mimeType);
            }
        }

        return false;
    }
}