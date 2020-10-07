<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 15/5/17
 * Time: 3:18 PM
 */

$teams = explode("\n", file_get_contents("data/input.txt"));

foreach ($teams as $line=>$team)
{
    $vals = explode(",", $team);
    foreach ($vals as $i=>$val)
    {
        if($i==0 || ($i>=3 && $i<=14))
        {
            continue;
        }
        echo $val . ",";
    }
    echo "<br>";
}
