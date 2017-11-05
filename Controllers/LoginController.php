<?php
/**
 * User: jmueller
 * Date: 5/11/17
 * Time: 8:27 AM
 */

namespace controllers;


use models\UsersModel;
use views\BaseTemplateView;
require_once 'BaseController.php';
require_once __DIR__ . '/../Views/BaseTemplateView.php';
require_once __DIR__ . '/../Models/UsersModel.php';
require_once __DIR__ . '/../Config/database.php';

class LoginController extends BaseController
{
    /**
     * Routing for login controller
     *
     * @param $request
     * @return string
     */
    public function respond($request)
    {
        switch($request['action']) {
            case 'authenticate':
                return $this->authenticate($request);
                break;
            case 'index':
            default:
                return $this->index();
        }
    }

    /**
     * Returns the login page
     *
     * @param string $errorMsg
     * @return string
     */
    private function index($errorMsg = '')
    {
        $errorMsg = !empty($errorMsg) ? "<div class='alert alert-danger'>$errorMsg</div>" : '';

        // html pulled and minorly edited from bootstrap login form example
        $loginForm = "
            <form class='form-signin' style='max-width: 330px; padding: 15px; margin: 0 auto;' action='/admin/login.php' method='POST'>
                $errorMsg
                <input type='hidden' name='action' id='action' value='authenticate'>
                <h2 class='form-signin-heading'>Please sign in</h2>
                <label for='inputEmail' class='sr-only'>Email address</label>
                <input type='email' id='inputEmail' name='email' class='form-control' placeholder='Email address' required='' autofocus=''>
                <label for='inputPassword' class='sr-only'>Password</label>
                <input type='password' id='inputPassword' name='password' class='form-control' placeholder='Password' required=''>
                <br />
                <button class='btn btn btn-primary btn-block' type='submit'>Sign in</button>
            </form>
        ";

        return BaseTemplateView::baseTemplateView('admin', $loginForm, '');
    }

    /**
     * Authenticates user
     *
     * @param array $request
     * @return string
     */
    private function authenticate($request)
    {
        $errorMsg = 'Login failed: invalid username or password';

        $user = UsersModel::getAdminUserForAuthentication($request['email'])[0];
        if (empty($user)) {
            return $this->index($errorMsg);
        }

        if (!password_verify($request['password'], $user['Password'])) {
            return $this->index($errorMsg);
        }

        session_start();
        $_SESSION['authenticated'] = true;
        var_dump($_SESSION);

        return "<script>location.href='/admin/admin.php';</script>;";
    }
}