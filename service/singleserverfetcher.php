<?php 
include("../service/serverfetcher.php");
include("../config/config.php");
if(isset($_GET["ip"]))
{
	$ip=$_GET["ip"];
	$ip_array = array();
	$ip_array[0] = $ip;
	$si=ServerInfoFetcher::FetchAllServerDetailsReturnJson($ip_array);
	echo json_encode(json_decode($si)[0]);
}
?>