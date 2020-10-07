<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 2/3/17
 * Time: 1:50 AM
 */

ini_set("display_errors", 1);
require_once ("functions.php");

$players = json_decode(safeReturn($_REQUEST, "players"));
$text = "";
foreach ($players as $player)
{
    $line = $player->name . "," . $player->basePrice . "," . $player->role . "," . $player->iplTeam ."," . $player->nationality . "," . (($player->isStar) ? "Yes" : "No");
    $text .= $line . "\n";
}
$text = substr($text, 0, strlen($text)-1);
file_put_contents("data/players.txt", $text);
echo $text;