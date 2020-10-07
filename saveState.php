<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 24/2/17
 * Time: 8:08 PM
 */

ini_set("display_errors", 1);

require_once "functions.php";

$round = safeReturn($_REQUEST, "round");
$auctionStateJson = safeReturn($_REQUEST, "state");

if(isStringSet($round) && isStringSet($auctionStateJson))
{
    $fileName = getAuctionStateFile($round);
    file_put_contents($fileName, $auctionStateJson);

    resetAuctionToRound($round);

    echo $fileName;
}
else
{
    echo "0";
    exit;
}