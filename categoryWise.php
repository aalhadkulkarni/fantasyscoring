<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 3/3/17
 * Time: 9:01 PM
 */

require_once "functions.php";

echo "<h2>Category wise summary</h2>";

$players = getPlayers();

$isStars = [true, false];
$nationalities = ["Indian", "Overseas"];
$roles = ["Batsman", "Bowler", "Keeper", "All Rounder"];

foreach ($isStars as $isStar)
{
    foreach ($nationalities as $nationality)
    {
        foreach ($roles as $role)
        {
            $curPlayers = array();
            foreach ($players as $player)
            {
                if($player->isStar == $isStar &&
                    $player->nationality == $nationality &&
                    $player->role == $role)
                {
                    $curPlayers[] = $player;
                }
            }
            echo "<b>Category - ";
            echo ($isStar) ? "Star " : " Ordinary ";
            echo $nationality . " ";
            echo $role . ": ";
            echo count($curPlayers) . " </b><br>";
            foreach ($curPlayers as $curPlayer)
            {
                echo $curPlayer->name . " (" . $curPlayer->iplTeam . ") - ";
                echo "Base price: " . $curPlayer->basePrice . "L<br>";
            }
        }
    }
}