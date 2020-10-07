<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 2/3/17
 * Time: 1:02 AM
 */

require_once "functions.php";

$players = getPlayers();
header("Content-Type: application/json", true);
echo json_encode($players);