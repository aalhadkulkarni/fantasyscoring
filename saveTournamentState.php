<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 5/4/17
 * Time: 8:46 PM
 */

require_once "functions.php";

$tournament = "ipl2017";

$input = safeReturn($_REQUEST, "state");

$matchNo = getMatchNo($tournament);

$matchNo++;

file_put_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/tournament_states/match" . $matchNo . ".txt", $input);
file_put_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/matchCount.txt", $matchNo);

echo "1";