<?php
/**
 * User: jmueller
 * Date: 7/10/17
 * Time: 4:47 PM
 */

namespace controllers;


abstract class BaseController
{
    abstract function respond($request);

    /**
     * Process the errors and return html message
     *
     * @param array $errors
     * @param int $code
     * @return string
     */
    protected function respondWithErrors(array $errors, $code)
    {
        http_response_code($code);

        $html = '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            $html .= "<li>$error</li>";
        }
        $html .= '</ul></div>';

        return $html;
    }
}