<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 27/4/17
 * Time: 2:46 AM
 */

require_once "functions.php";

$tournament = "ipl2017";
$matchCount = file_get_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/matchCount.txt");
$matchCount++;

$str = file_get_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/tournament_states/match" . $matchCount . ".txt");
$matchState = json_decode($str, true);

$teams = $matchState["leagues"][0]["leagueTeams"];
foreach ($teams as $team)
{
    $name = $team["ownerName"] . " (" . $team["teamName"] . ")";
    $transfers = 0;
    $ccCount = 0;
    $vccCount = 0;
    $pnw = 0;

    $str = implode(",", array($name, $transfers, $ccCount, $vccCount, $pnw));
    echo $str . "<br>";
}