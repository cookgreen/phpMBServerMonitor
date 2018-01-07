<?php
Include("mb_server_info.php");
Include("mb_server_monitor_config.php");

class ServerInfoFetcher
{
	static function FetchServerIPList($type)
	{
		try
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
		catch(Exception $e)
		{
			echo 'Error: ' . $e->Message;
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
		try
		{
		set_time_limit(90); 
		$server_info_array=array();
		$index=0;
		
		$curl_array = array();
		$master = curl_multi_init();
		$running = 1;
		$count = count($ip_array);
		
		for($i = 0;$i < $count;$i++)
		{
			$curl_array[$i] = curl_init($ip_array[$i]);
			curl_setopt($curl_array[$i], CURLOPT_FOLLOWLOCATION, true); 
			curl_setopt($curl_array[$i],CURLOPT_FRESH_CONNECT,true); 
			curl_setopt($curl_array[$i],CURLOPT_CONNECTTIMEOUT,10); 
			curl_setopt($curl_array[$i],CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl_array[$i],CURLOPT_TIMEOUT,30); 
			
			curl_multi_add_handle($master, $curl_array[$i]); 
		}
		do
		{
			curl_multi_exec($master, $running);
			
			$info = curl_multi_info_read($master); 
            if($info['handle'])
			{ 
                $finalresult[] = curl_multi_getcontent($info['handle']); 
                $returnedOrder[] = array_search($info['handle'], $curl_array, true); 
                curl_multi_remove_handle($master, $info['handle']); 
                curl_close($curl_array[end($returnedOrder)]);
				$index = end($returnedOrder);
				$serverXMLData=simplexml_load_string($finalresult[$index]);
				$serverString = '';
				if($serverXMLData!==false)
				{
					$serverString .= "<tr>";
					$serverString .= '<td width="150" align="center">' . $ip_array[$index] . '</td>';
					$serverString .= '<td width="300" align="center">' . $serverXMLData->Name . '</td>';
					$serverString .= '<td width="100" align="center">' . $serverXMLData->ModuleName . '</td>';
					$serverString .= '<td width="100" align="center">' . $serverXMLData->MapTypeName . '</td>';
					$serverString .= '<td width="100" align="center">' . $serverXMLData->MapName . '</td>';
					$serverString .= '<td width="50" align="center">' . $serverXMLData->NumberOfActivePlayers . '/'. $serverXMLData->MaxNumberOfPlayers . '</td>';
					$serverString .= '<td width="50" align="center">' . $serverXMLData->HasPassword . '</td>';
					$serverString .= '<td width="100" align="center"><a href="mb_single_server_monitor.php?ip=' . $ip_array[$index] . '">View</a></td>';
					$serverString .= "</tr>";
					echo $serverString;
				}
				else
				{
					continue;
				}
				
				ob_flush();
				flush(); 
            } 
		}while($running > 0);
		return $server_info_array;
		}
		catch(Exception $e)
		{
			echo $e->Message;
		}
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
	
	function Ping($address)
	{
		$ping = exec('ping '. $address);
		$chunks = explode(' ', $ping);
		$avg = substr($chunks[10], 0, strlen($chunks[10])-2);
		echo $avg;
		return $avg;
	}
}

?>