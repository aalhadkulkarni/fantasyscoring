<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 21/5/17
 * Time: 10:45 PM
 */

ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once "functions.php";

$state = json_decode(file_get_contents(TOURNAMENTS_HOME_DIR . "/ipl2017/tournament_states/match60.txt"), true);
$teams = $state["leagues"][0]["leagueTeams"];
$pnwLines = explode("\n",file_get_contents("data/pnw.txt"));

foreach ($pnwLines as $pnwLine)
{
    $pnw = explode(" ", $pnwLine);
    $name = $pnw[0];
    $points = $pnw[1];
    if(count($pnw) == 3)
    {
        $name = $pnw[0] . " " . $pnw[1];
        $points = $pnw[2];
    }
    $name = trim($name);
    $found = false;
    foreach ($teams as $team)
    {
        if(strcasecmp(trim($team["ownerName"]), trim($name)) == 0)
        {
            $found = true;
            echo $name . " ";
            $ccCount = safeReturn($team, "ccCount", 0);
            $vccCount = safeReturn($team, "vccCount", 0);
            $extra = 0;
            $extra += max(0, ($ccCount-7));
            $extra += max(0, ($vccCount-7));
            $extra += max(0, (count($team["leaguePlayers"])-22));
            $extra*=100;
            $points -= $extra;
            echo $points . "<br>";
        }
    }
    if(!$found)
    {
        echo $name . "<br>";
    }
}