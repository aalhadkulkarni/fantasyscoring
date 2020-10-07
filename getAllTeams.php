<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 1/4/17
 * Time: 6:29 PM
 */

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once "functions.php";

header("Content-Type: application/json", true);

$teamsData = file_get_contents("data/temp/allTeams.txt");
$teamsData = explode("\n", $teamsData);
$teams = array();
foreach ($teamsData as $index=>$line)
{
    if($index==0) continue;
    $team = getTeamJson($line, $teamsData[0], true);
    if($team != null)
    {
        $teams[] = $team;
    }
}

echo json_encode($teams, true);