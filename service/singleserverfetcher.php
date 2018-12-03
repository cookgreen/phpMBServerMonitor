<?php 
require_once("../service/serverfetcher.php");
require_once("../config/config.php");
if(isset($_GET["ip"]))
{
	$ip=$_GET["ip"];
	
	$fetcher = new ServerInfoFetcher();
	
	$si=$fetcher->FetchServerDetailsByIP($ip);
	echo json_encode($si);
}
?>