<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 2/6/17
 * Time: 2:09 AM
 */

require_once "functions.php";

$teams = array
(
    "Thane" => array(),
    "Miraj" => array(),
    "Kolhapur" => array(),
    "Pune" => array(),
    "Karad" => array(),
);

foreach ($teams as $team => $xi)
{
    $teamCsv = file_get_contents("data/" . $team . ".txt");
    $teamXI = explode(",", $teamCsv);
    $teams[$team] = $teamXI;
}

header("Content-Type: application/json", true);
echo json_encode($teams, true);