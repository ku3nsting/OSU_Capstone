<?php

function html($value)
{
    return htmlentities($value, ENT_QUOTES, ini_get("default_charset"), false);
}
