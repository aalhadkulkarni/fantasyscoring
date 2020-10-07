<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 27/4/17
 * Time: 3:05 AM
 */

require_once "functions.php";

$lines = explode("\n", file_get_contents("data/pnw.csv"));

$entries = array();
foreach ($lines as $i => $line)
{
    if($i == 0) continue; //Headings
    if(!isStringSet(trim($line))) continue; //Empty line
    $info = explode(",", $line);
    $entry = array
    (
        "name" => safeReturn($info, 0),
        "transfersRemaining" => safeReturn($info, 1),
        "ccRemaining" => safeReturn($info, 2),
        "vccRemaining" => safeReturn($info, 3),
        "points" => safeReturn($info, 4),
    );
    $entries[] = $entry;
}
header("Content-Type: application/json", true);
echo json_encode($entries);