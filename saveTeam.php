<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 1/4/17
 * Time: 8:44 PM
 */

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once "functions.php";

$teamId = safeReturn($_REQUEST, "teamId");
$team = safeReturn($_REQUEST, "team");

//var_export($team); die();
$team = json_decode($team);
$teamLine = "";
foreach ($team as $header=>$val)
{
    if($header == "headers") continue;
    $val = explode(",", $val);
    $val = implode(" ", $val);
    $teamLine .= $val . ",";
}
$teamLine = substr($teamLine, 0, strlen($teamLine)-1);
file_put_contents("data/temp/" . $teamId . ".txt", $teamLine);

echo "1";