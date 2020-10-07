<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 4/3/17
 * Time: 7:28 PM
 */

require_once "functions.php";

if(true || isUserAdmin())
{
    echo file_get_contents("html/auction.html");
}
else
{
    echo file_get_contents("html/login.html");
}