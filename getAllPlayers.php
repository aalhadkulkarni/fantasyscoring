<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 1/4/17
 * Time: 7:53 PM
 */

require_once "functions.php";

$players = getTournamentPlayers("ipl2017");
$playerNames = array();
foreach ($players as $player)
{
    $playerNames[] = $player->name . " (" . $player->cricketTeam . ")";
}

header("Content-Type: application/json", true);
echo json_encode($playerNames, true);