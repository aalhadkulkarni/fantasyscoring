<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 21/3/17
 * Time: 7:41 PM
 */

require_once "functions.php";

$tournament = "ipl2017"; //TODO: Get this from $_GET

$tournamentState = new TournamentState();

$scoringRules = getRules($tournament);

$leagues = getTournamentLeagues($tournament);
foreach ($leagues as $league)
{
    $teams = getLeagueTeams($tournament, $league->id);
    $league->leagueTeams = $teams;
}

$players = getTournamentPlayers($tournament);

$tournamentState->scoringRules = $scoringRules;
$tournamentState->leagues = $leagues;
$tournamentState->players = $players;

$tournamentStateStr = json_encode($tournamentState);
echo $tournamentStateStr . "<br>";
echo strlen($tournamentStateStr);
//file_put_contents(TOURNAMENTS_HOME_DIR . "/tournament_states/match0.txt", json_encode($tournamentState));