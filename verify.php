<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 27/4/17
 * Time: 1:14 AM
 */

require_once "functions.php";

$tournament = "ipl2017";
$matchCount = file_get_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/matchCount.txt");

$matchStates = array();

for($i=0; $i<=$matchCount; $i++)
{
    $str = file_get_contents(TOURNAMENTS_HOME_DIR . "/" . $tournament . "/tournament_states/match" . $i . ".txt");
    $matchState = json_decode($str, true);

    $matchStates[] = $matchState;
}

$prevMatchState = null;
foreach ($matchStates as $matchNo => $matchState)
{
    if($prevMatchState!=null)
    {
        $curTeams = $matchState["leagues"][0]["leagueTeams"];
        $prevTeams = $prevMatchState["leagues"][0]["leagueTeams"];

        foreach ($curTeams as $id => $curTeam)
        {
            $prevTeam = $prevTeams[$id];

            $curCcCount = safeReturn($curTeam, "ccCount", 0);
            $prevCcCount = safeReturn($prevTeam, "ccCount", 0);
            $ccDiff = abs($curCcCount - $prevCcCount);
            $curCaptainId = $curTeam["curCaptainId"];
            $prevCaptainId = $prevTeam["curCaptainId"];
            if($ccDiff>0)
            {
                if($curCaptainId == $prevCaptainId)
                {
                    echo "CAPTAIN - ";
                    echo $curTeam["ownerName"] . " (" . $curTeam["teamName"] . ") - ";
                    echo $prevCcCount . " (in match " . ($matchNo-1) . ") - ";
                    echo $curCcCount . " (in match " . ($matchNo) . ") - ";
                    echo "The captain is same";
                    echo "<br>";
                }
                else if($ccDiff > 1)
                {
                    echo "CAPTAIN - ";
                    echo $curTeam["ownerName"] . " (" . $curTeam["teamName"] . ") - ";
                    echo $prevCcCount . " (in match " . ($matchNo-1) . ") - ";
                    echo $curCcCount . " (in match " . ($matchNo) . ")";
                    echo "<br>";
                }
            }

            $curVccCount = safeReturn($curTeam, "vccCount", 0);
            $prevVccCount = safeReturn($prevTeam, "vccCount", 0);
            $vccDiff = abs($curVccCount - $prevVccCount);
            $curViceCaptainId = $curTeam["curViceCaptainId"];
            $prevViceCaptainId = $prevTeam["curViceCaptainId"];
            if($vccDiff>0)
            {
                if($curViceCaptainId == $prevViceCaptainId)
                {
                    echo "VICE CAPTAIN - ";
                    echo $curTeam["ownerName"] . " (" . $curTeam["teamName"] . ") - ";
                    echo $prevVccCount . " (in match " . ($matchNo-1) . ") - ";
                    echo $curVccCount . " (in match " . ($matchNo) . ") - ";
                    echo "The vice captain is same";
                    echo "<br>";
                }
                else if($vccDiff > 1)
                {
                    echo "VICE CAPTAIN - ";
                    echo $curTeam["ownerName"] . " (" . $curTeam["teamName"] . ") - ";
                    echo $prevVccCount . " (in match " . ($matchNo-1) . ") - ";
                    echo $curVccCount . " (in match " . ($matchNo) . ")";
                    echo "<br>";
                }
            }
        }
    }

    $prevMatchState = $matchState;
}