<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 6/4/17
 * Time: 12:39 AM
 */

require_once "functions.php";

$tournament = "ipl2017";

$matchNo = getMatchNo($tournament);
$nextMatchNo = $matchNo+1;

$lastMatchFile = TOURNAMENTS_HOME_DIR . "/" . $tournament . "/tournament_states/match" . $matchNo . ".txt";
$nextMatchFile = TOURNAMENTS_HOME_DIR . "/" . $tournament . "/tournament_states/match" . $nextMatchNo . ".txt";

if(!file_exists($nextMatchFile))
{
    file_put_contents($nextMatchFile, file_get_contents($lastMatchFile));
}

header("Content-Type: application/json", true);
echo file_get_contents($nextMatchFile);