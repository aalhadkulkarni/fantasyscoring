<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 6/4/17
 * Time: 1:44 AM
 */

ini_set("display_errors", "On");

require_once "functions.php";

$output = "0";

$teamId = safeReturn($_REQUEST, "teamId");
$teamName = safeReturn($_REQUEST, "teamName");

$teamText = file_get_contents(TOURNAMENTS_HOME_DIR . "/ipl2017/individual/teams.txt");
$teams = explode("\n", $teamText);
foreach ($teams as $index=>$team)
{
    if($index==0 || strlen(trim($team))==0) continue; //Header row
    $curTeam = explode(",", $team);
    $curTeamId = getTeamId($curTeam[0]);
    $curTeamName = $curTeam[2];
    if($teamId === $curTeamId && $teamName == $curTeamName)
    {
        $output = "1";
        break;
    }
}

echo $output;