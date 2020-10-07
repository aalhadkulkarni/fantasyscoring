<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 23/4/17
 * Time: 2:01 AM
 */

require_once "functions.php";

$matchNo = safeReturn($_REQUEST, "m");
$html = file_get_contents("html/getSpecificLb.html");
$html = str_replace("{{matchNo}}", $matchNo, $html);
echo $html;