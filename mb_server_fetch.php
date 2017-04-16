<?php
Include("mb_server_info.php");
Include("mb_server_monitor_config.php");

class ServerInfoFetcher
{
	static function FetchServerIPList($type)
	{
		$url=null;
		if($type==0)//warband
		{
			$url="http://warbandmain.taleworlds.com/handlerservers.ashx?type=list&gametype=warband";
		}
		else if($type==1)//wfas
		{
			$url="http://warbandmain.taleworlds.com/handlerservers.ashx?type=list&gametype=wfas";
		}
		if($url!=null)
		{
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url); 
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($curl);  
			curl_close($curl);
			$server_ip_array=explode("|",$data);
			return $server_ip_array;
		}
	}
	static function FetchXMLData($ip)
	{
		$curl=curl_init();
		curl_setopt($curl, CURLOPT_URL, $ip); 
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
		$data = curl_exec($curl);
		return $data;
	}
	static function FetchServerDetailsByIP($ip)
	{
		if($ip!=null)
		{
			$data=ServerInfoFetcher::FetchXMLData($ip);
			$serverXMLData=simplexml_load_string($data);
			//echo $serverXMLData;
			if($serverXMLData!==false)
			{
				$si=new ServerInfo();
				$si->{"server_ip"}=	$ip;
				$si->{"server_name"}=	$serverXMLData->Name;
				$si->{"server_module"}=$serverXMLData->ModuleName;
				$si->{"server_mode"}=	$serverXMLData->MapTypeName;
				$si->{"server_map"}=	$serverXMLData->MapName;
				$si->{"server_player_nums"}=$serverXMLData->NumberOfActivePlayers;
				$si->{"server_max_player_nums"}=$serverXMLData->MaxNumberOfPlayers;
				$si->{"isLocked"}=		$serverXMLData->HasPassword;
				return $si;
			}
			else
			{
				return null;
			}
		}
	}
	
	static function FetchAllServerDetails($ip_array)
	{
		$server_info_array=array();
		$index=0;
		foreach($ip_array as $current_ip)
		{
			
			$si=ServerInfoFetcher::FetchServerDetailsByIP($current_ip);
			$server_info_array[$index]=$si;
			$index++;
		}
		return $server_info_array;
	}
	
	static function FetchServerDetailsByIPTest($ip)
	{
		if($ip!=null)
		{
			$data=ServerInfoFetcher::FetchXMLData($ip);
			echo $data;
			$serverXMLData=new SimpleXMLElement($data);
			echo $serverXMLData->ModuleName;
		}
	}
}

?>