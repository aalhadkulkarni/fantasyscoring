<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 17/3/17
 * Time: 12:44 AM
 */

ini_set("display_errors", "On");
require_once "functions.php";

header("Content-Type: application/json", true);

$tournament = "ipl2017";
$matchNo = getMatchNo($tournament);
if($matchNo == 0) //Init tournament
{
    $tournamentState = new TournamentState();
    $scoringRules = getRules($tournament);
    $leagues = getTournamentLeagues($tournament);
    foreach ($leagues as $league)
    {
        $teams = getLeagueTeams($tournament, $league->id);
        $league->leagueTeams = $teams;
    }
    $tournamentPlayers = getTournamentPlayers($tournament);
    $tournamentState->scoringRules = $scoringRules;
    $tournamentState->leagues = $leagues;
    $tournamentState->players = $tournamentPlayers;

    $output = json_encode($tournamentState);
    file_put_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/tournament_states/match" . $matchNo . ".txt", $output);
    echo $output;
}
else
{
    $output = file_get_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/tournament_states/match" . $matchNo . ".txt");
    echo $output;
}