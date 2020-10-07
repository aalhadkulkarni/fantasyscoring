<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 7/4/17
 * Time: 8:39 PM
 */

require_once "functions.php";

header("Content-Type: application/json", true);

$tournament = "ipl2017";
$matchNo = getMatchNo($tournament);

$jpScores = [];
for($i=1; $i<=$matchNo; $i++)
{
    $tournamentState = json_decode(file_get_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/tournament_states/match" . $i . ".txt"), true);
    $jpScores[strval($i)] = $tournamentState["jackpotScore"];
}

echo json_encode($jpScores);