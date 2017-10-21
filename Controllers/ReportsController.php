<?php
/**
 * User: jmueller
 * Date: 7/10/17
 * Time: 4:42 PM
 */

namespace controllers;


use views\BaseTemplateView;
use views\ReportsViews;

require_once 'BaseController.php';
require_once __DIR__ . '/../Views/ReportsViews.php';
require_once __DIR__ . '/../Views/BaseTemplateView.php';

class ReportsController extends BaseController
{
    function respond($request)
    {
        switch ($request['action']) {
            case 'run-query':
                return self::runQuery($request);
                break;
            case 'index':
            default:
                return self::index();
                break;
        }
    }

    /**
     * Return the Admin Reports Page
     * @return string
     */
    private function index()
    {
        // make necessary queries calls through models
        // return views related to the initial reports landing page
        return BaseTemplateView::baseTemplateView(
            'admin',
            ReportsViews::indexView(),
            'report.init();'
        );
    }

    /**
     * @param $request
     */
    private function runQuery($request)
    {
        // Base query for the query builder
        $query = "
            SELECT Awards.AwardLabel, Awards_Given.AwardDate,
              Employees.fName, Employees.lName, Employees.Email, Employees.hireDate,
              Giver.fName GiverFirstName, Giver.lName GiverLastName, Giver.Email GiverEmail
            FROM Awards_Given
            JOIN Awards ON Awards_Given.AwardID = Awards.ID
            JOIN Employees ON Awards_Given.AwardedByID = Employees.ID
            JOIN Employees as Giver ON Awards_Given.AwardedByID = Giver.ID
            JOIN UserType ON Employees.ID = UserType.EmployeeID
            WHERE UserType.Type = 'user'
        ";

        //TODO: process select fields and where rules
    }
}