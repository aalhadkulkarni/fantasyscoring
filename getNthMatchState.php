<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 23/4/17
 * Time: 12:27 AM
 */

ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once "functions.php";

$matchNo = safeReturn($_REQUEST, "matchNo");
$tournament = "ipl2017";
$matchCount = file_get_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/matchCount.txt");
$matchNo = min($matchNo, $matchCount);
header("Content-Type: application/json", true);
echo file_get_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/tournament_states/match" . $matchNo . ".txt");