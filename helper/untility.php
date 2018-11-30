<?php
class Utility
{
	static function Ping($address)
	{
		$ping = exec('ping '. $address);
		$chunks = explode(' ', $ping);
		$avg = substr($chunks[10], 0, strlen($chunks[10])-2);
		return $avg;
	}
	
	static function guid()
	{
		mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $uuid = substr($charid, 0, 8)
        .substr($charid, 8, 4)
        .substr($charid,12, 4)
        .substr($charid,16, 4)
        .substr($charid,20,12);
    return $uuid;
	}
}
?>