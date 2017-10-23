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
}