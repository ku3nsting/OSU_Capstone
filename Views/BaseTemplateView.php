<?php
/**
 * User: jmueller
 * Date: 7/10/17
 * Time: 12:30 PM
 */

namespace views;


class BaseTemplateView
{
    /**
     * Returning the base html for the website with the navigation links and assets included
     * @param string $type - options are 'user' or 'admin
     * @param string $html
     * @param string $scripts
     * @return string
     */
    public static function baseTemplateView($type, $html = '', $scripts = '')
    {
        $userLinks = [
            'manage-account' => [
                'href' => '#',
                'text' => 'Manage Account'
            ],
            'manage-my-awards' => [
                'href' => '#',
                'text' => 'Manage My Awards'
            ],
            'nominate' => [
                'href' => '#',
                'text' => 'Nominate'
            ],
        ];

        if (!empty($_SESSION['authenticated'])) {
            $adminLinks = [
                'manage-users' => [
                    'href' => '/admin/manage-users.php',
                    'text' => 'Manage Users'
                ],
                'reports' => [
                    'href' => '/admin/reports.php',
                    'text' => 'Reports'
                ],
            ];
        } else {
            $adminLinks = [
                'login' => [
                    'href' => '/admin/login.php',
                    'text' => 'Login'
                ]
            ];
        }

        $navBarLinks = '';
        switch ($type) {
            case 'user':
                $navBarLinks = self::navBarList($userLinks);
                break;
            case 'admin':
                $navBarLinks = self::navBarList($adminLinks);
                break;
        }

        return '
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Gemini Admin</title>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Query Builder -->
        <link rel="stylesheet" href="../assets/css/query-builder.default.min.css">
        <!-- Bootswatch theme -->
        <link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/sandstone/bootstrap.min.css" rel="stylesheet" integrity="sha384-G3G7OsJCbOk1USkOY4RfeX1z27YaWrZ1YuaQ5tbuawed9IoreRDpWpTkZLXQfPm3" crossorigin="anonymous">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn\'t work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    </head>
    <body>
        <div class="container">
          <nav class="navbar navbar-default">
            <div class="container-fluid">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/admin/admin.php">Gemini Admin</a>
              </div>
              <div id="navbar" class="navbar-collapse collapse">
                ' . $navBarLinks . '
                <ul class="nav navbar-nav navbar-right">
                  <li><a href="/admin/logout.php">Sign Out</a></li>
                </ul>
              </div><!--/.nav-collapse -->
            </div><!--/.container-fluid -->
          </nav>
        </div>

        <div class="container" id="main-content"> ' . $html . '</div>


        <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="../assets/js/query-builder.standalone.min.js"></script>
        <script src="../assets/js/reports.js"></script>
        <script src="../assets/js/manage-users.js"></script>
        <script>
            $(document).ready(function() {
                ' . $scripts. '
            });
        </script>
    </body>
</html>
        ';
    }

    /**
     * @param array $links - an array of arrays with the following format
     *      $links = [
     *          'home' => [
     *              'href' => '#',
     *              'text' => 'Home',
     *          ],
     *          ...
     *      ]
     * @return string
     */
    public static function navBarList($links)
    {
        $navBarList = '<ul class="nav navbar-nav">';

        foreach ($links as $id => $link) {
            $navBarList .= "<li><a href='{$link['href']}'>{$link['text']}</a></li>";
        }

        $navBarList .= '</ul>';

        return $navBarList;
    }
}