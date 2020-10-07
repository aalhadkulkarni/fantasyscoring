<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 1/6/17
 * Time: 10:24 PM
 */

$teamName = $_REQUEST["team"];
$xi = $_REQUEST["xi"];

if(file_put_contents("data/" . $teamName . ".txt", $xi))
{
    echo "1";
}
else
{
    echo "0";
}