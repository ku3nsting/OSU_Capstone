<?php
/**
 * User: jmueller
 * Date: 28/10/17
 * Time: 2:40 PM
 */

namespace controllers;


use models\UsersModel;
use mysqli;
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
}