<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 17/3/17
 * Time: 12:44 AM
 */

ini_set("display_errors", "On");
require_once "functions.php";
/*
header("Content-Type: application/json", true);

$tournament = "ipl2017";
$matchNo = getMatchNo($tournament);
$tournamentState = new TournamentState();
$scoringRules = getRules($tournament);
$leagues = getTournamentLeagues($tournament);
foreach ($leagues as $league)
{
    $teams = getLeagueTeams($tournament, $league->id);
    $league->leagueTeams = $teams;
}
$tournamentPlayers = getTournamentPlayers($tournament);
$tournamentState->leagues = $leagues;

$output = json_encode($tournamentState);
echo $output;*/
