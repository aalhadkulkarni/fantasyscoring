<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 4/3/17
 * Time: 8:11 PM
 */

require_once "functions.php";

if(isStringSet(safeReturn($_REQUEST, "adminId")))
{
    $adminId = safeReturn($_REQUEST, "adminId");
    $password = safeReturn($_REQUEST, "password");
    if(key_exists($adminId, $admins) && $password == safeReturn($admins, $adminId))
    {
        setcookie("auction_admin", $adminId);
        echo file_get_contents("html/index.html");
    }
    else
    {
        echo file_get_contents("html/relogin.html");
    }
}
else
{

}