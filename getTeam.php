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

$teamId = safeReturn($_REQUEST, "teamId");
$teamName = safeReturn($_REQUEST, "teamName");

/*$teamId = "004907";
$teamName = "Eakach waada Zaheer dada";*/

$teamsData = file_get_contents("data/temp/allTeams.txt");
$teamsData = explode("\n", $teamsData);

$team = null;
if(file_exists("data/temp/" . $teamId . ".txt"))
{
    $teamLine = file_get_contents("data/temp/" . $teamId . ".txt");
    $team = getTeamJson($teamLine, $teamsData[0]);
    if($team["Team Name"] != $teamName)
    {
        echo json_encode(array("error"=>"1"), true);
        die();
    }
}
else
{
    foreach ($teamsData as $index=>$line)
    {
        if($index==0) continue;
        $team = getTeamJson($line, $teamsData[0]);
        $curTeamId = getTeamId($team["Timestamp"]);
        if($curTeamId===$teamId && $team["Team Name"]==$teamName)
        {
            break;
        }
        $team = null;
    }
}

if($team==null)
{
    echo json_encode(array("error"=>"1"), true);
    die();
}

echo json_encode($team, true);