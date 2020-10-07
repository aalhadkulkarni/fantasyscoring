<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 22/5/17
 * Time: 12:41 AM
 */

ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once "functions.php";

$tournamentState = json_decode(file_get_contents(TOURNAMENTS_HOME_DIR . "/ipl2017/tournament_states/match64.txt"), true);

$teams = $tournamentState["leagues"][0]["leagueTeams"];

$pnwScores = explode("\n", file_get_contents("data/pnw.txt"));

foreach ($pnwScores as $pnwScore)
{
    $info = explode(",", $pnwScore);
    $found = false;
    foreach ($tournamentState["leagues"][0]["leagueTeams"] as $id => $team)
    {
        if(strcasecmp($team["ownerName"], $info[0]) == 0)
        {
            $found = true;
            $points = $info[1];
            $tournamentState["leagues"][0]["leagueTeams"][$id]["points"] += $points;
        }
    }
}

file_put_contents(TOURNAMENTS_HOME_DIR . "/ipl2017/tournament_states/match64.txt", json_encode($tournamentState));