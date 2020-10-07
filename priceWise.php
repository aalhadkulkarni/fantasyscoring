<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 3/3/17
 * Time: 9:01 PM
 */

require_once "functions.php";

echo "<h2>Price wise summary</h2>";

$players = getPlayers();

for($x=8; $x>=1; $x--)
{
    $curPlayers = array();
    foreach ($players as $player)
    {
        if($player->basePrice == $x)
        {
            $curPlayers[] = $player;
        }
    }
    echo "<b>Base price - " . $x . "L: " . count($curPlayers) . "</b><br>";
    foreach ($curPlayers as $curPlayer)
    {
        echo $curPlayer->name . " (" . $curPlayer->iplTeam . ") - ";
        echo ($curPlayer->isStar) ? "Star " : "Ordinary ";
        echo $curPlayer->role;
        echo "<br>";
    }
}