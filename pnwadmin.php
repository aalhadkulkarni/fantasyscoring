<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 28/4/17
 * Time: 3:52 PM
 */

require_once "functions.php";

echo file_get_contents("html/pnwadmin.html");

$success = safeReturn($_REQUEST, "success");
if($success=="1")
{
    echo "<script>alert('Upload successful.')</script>";
}
else if($success == "0")
{
    echo "<script>alert('Upload failed. Please try again.');</script>";
}