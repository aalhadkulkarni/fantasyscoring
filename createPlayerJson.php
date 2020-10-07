<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 30/3/18
 * Time: 7:49 PM
 */

//ini_set("display_errors", 1);

$state = json_decode(file_get_contents("state.json"));

foreach ($state->matches as $matchId => $match)
{
    $state->matches->$matchId->totalPointsScored = 0;
}

echo json_encode($state);