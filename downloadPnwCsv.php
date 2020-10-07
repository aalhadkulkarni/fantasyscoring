<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 28/4/17
 * Time: 3:52 PM
 */

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=pnw.csv");
header("Pragma: no-cache");
header("Expires: 0");
echo file_get_contents("data/pnw.csv");