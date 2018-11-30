<?php 
require_once("../service/serverfetcher.php");
require_once("../config/config.php");
if(isset($_GET["ip"]))
{
	$ip=$_GET["ip"];
	$ip_array = array();
	$ip_array[0] = $ip;
	
	$fetcher = new ServerInfoFetcher();
	
	$si=$fetcher->FetchAllServerDetailsReturnJson($ip_array);
	echo json_encode(json_decode($si)[0]);
}
?>