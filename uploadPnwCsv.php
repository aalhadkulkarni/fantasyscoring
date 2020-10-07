<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 28/4/17
 * Time: 4:20 PM
 */

require_once "functions.php";

$pnwCsv = file_get_contents($_FILES["pnw_csv"]["tmp_name"]);

$success = "1";
try
{
    if(!file_put_contents("data/pnw.csv", $pnwCsv))
    {
        $success = "0";
    }
}
catch(Exception $e)
{
    $success = "0";
}

header("Location: pnwadmin.php?success=" . $success);