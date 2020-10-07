<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 2/3/17
 * Time: 12:55 AM
 */

require_once "functions.php";

if(isUserAdmin())
{
    echo file_get_contents("html/players.html");
}
else
{
    echo file_get_contents("html/login.html");
}