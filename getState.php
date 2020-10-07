<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 24/2/17
 * Time: 7:45 PM
 */

ini_set("display_errors", 1);
require_once "functions.php";

$round = safeReturn($_REQUEST, "round");

$totalRounds = getTotalRounds();
$round = isStringSet($round) ? $round : $totalRounds;

$auctionStateJson = "";

$fileName = getAuctionStateFile($round, true);
if(isset($_REQUEST["hardreset"]))
{
    $round = 1;
    $fileName = getAuctionStateFile($round, false);
    $auctionStateJson = getInitialState();
    file_put_contents($fileName, $auctionStateJson);
    resetAuctionToRound($round);
    setTotalRounds($round);
    return;
}
else if(isStringSet($fileName))
{
    $auctionStateJson = file_get_contents($fileName);
}
else if($round==1)
{
    $fileName = getAuctionStateFile($round, false);
    $auctionStateJson = getInitialState();
    file_put_contents($fileName, $auctionStateJson);
    setTotalRounds($round);
}
else
{
    echo "";
    exit;
}

if(isset($_REQUEST["reset"]))
{
    resetAuctionToRound($round);
}
header("Content-Type: application/json", true);
echo $auctionStateJson;