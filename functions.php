<?php
/**
 * Created by PhpStorm.
 * User: aalhadk
 * Date: 2/4/21
 * Time: 2:45 AM
 */

/**
 * Combining 2 most commonly used conditions to check blank or null string
 *
 * @param string $string
 * @return boolean
 */
function isStringSet($string)
{
    return (!is_null($string) && $string !== '');
}

function safeReturn($array, $index, $default=null)
{
    return isset($array[$index]) ? $array[$index] : $default;
}

function getRequestParam($param)
{
    return safeReturn($_REQUEST, $param);
}