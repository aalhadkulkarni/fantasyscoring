<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 5/4/17
 * Time: 1:07 AM
 */

ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once "functions.php";

$teamsData = file_get_contents("data/temp/allTeams.txt");
$teamsData = explode("\n", $teamsData);

$output = "";

foreach ($teamsData as $index=>$line)
{
    if($index==0)
    {
        $output .= $line . "\n";
        continue;
    }
    $team = getTeamJson($line, $teamsData[0]);
    $curTeamId = getTeamId($team["Timestamp"]);
    if(file_exists("data/temp/" . $curTeamId . ".txt"))
    {
        $teamLine = file_get_contents("data/temp/" . $curTeamId . ".txt");
        $output .= $teamLine . "\n";
    }
    else
    {
        $output .= $line . "\n";
    }
}

file_put_contents("data/tournaments/ipl2017/individual/teams.txt", $output);
echo $output;