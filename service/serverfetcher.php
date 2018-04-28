<?php
include("../entity/serverinfo.php");
include("../config/config.php");

class ServerInfoFetcher
{
	/*
	* @Description: Fetch Server List from official master server
	* @Input: Game Type; 0 - Mount&Blade Warband; 1 - Mount&Blade With Fire and Sword
	* @Output: Server Information Object
	*/
	public function FetchServerIPList($type)
	{
		try
		{
			$url=null;
			$typeStr=null;
			if($type==0)//warband
			{
				$typeStr = "warband";
			}
			else if($type==1)//wfas
			{
				$typeStr="wfas";
			}
			$url="http://warbandmain.taleworlds.com/handlerservers.ashx?type=list&gametype=" . $typeStr;
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
			return FALSE;
		}
	}
	
	/*
	* @Description: Fetch Server XML Data by specific ip address
	* @Input: Ip
	* @Output: Server XML Data string
	*/
	private function FetchXMLData($ip)
	{
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $ip); 
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	/*
	* @Description: Fetch Server Information by specific ip address
	* @Input: Ip
	* @Output: Server Information Object
	*/
	public function FetchServerDetailsByIP($ip)
	{
		if($ip!=null)
		{
			$xml=$this->FetchXMLData($ip);
			$serverXMLData=simplexml_load_string($xml);
			if($serverXMLData && !empty($serverXMLData->Mame))
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
	
	/*
	* @Description: Fetch All Server Information and return Json format
	* @Input: Ip Array
	* @Output: Json Data Format
	*/
	public function FetchAllServerDetailsReturnJson($ip_array)
	{
		try
		{
			set_time_limit(0); 
			$server_info_array=array();
			$index=0;
			$cur_idx=0;
			
			$curl_array = array();
			$master = curl_multi_init();
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
			
			$previoisActive = -1;
			$finalresult = array();
			$returnedOrder = array();
			
			do
			{
				curl_multi_exec($master, $running);
				if($running != $previoisActive)
				{
					$info = curl_multi_info_read($master); 
					$ch = $info['handle'];
					if($ch)
					{ 
						$finalresult[] = curl_multi_getcontent($ch);
						$returnedOrder[] = array_search($ch, $curl_array, true);
						$index = end($returnedOrder);
						curl_multi_remove_handle($master, $ch); 
						curl_close($curl_array[$index]);
						$serverXMLData=simplexml_load_string(end($finalresult));
						$serverString = '';
						if($serverXMLData and !empty($serverXMLData->Name))
						{
							$si = new ServerInfo();
							$si->server_ip = $ip_array[$index];
							$si->server_name = $serverXMLData->Name->__toString();
							$si->server_module = $serverXMLData->ModuleName->__toString();
							$si->server_mode = $serverXMLData->MapTypeName->__toString();
							$si->server_map = $serverXMLData->MapName->__toString();
							$si->server_player_nums = $serverXMLData->NumberOfActivePlayers->__toString() . '/'. $serverXMLData->MaxNumberOfPlayers->__toString();
							$si->isLocked = $serverXMLData->HasPassword->__toString();
							$server_info_array[$cur_idx] = $si;
							$cur_idx++;
						}
						else
						{
							continue;
						}
					} 
				}
			}while($running > 0);
			
			return json_encode($server_info_array);
		}
		catch(Exception $e)
		{
			return FALSE;
		}
	}
}

if(isset($_GET["type"]))
{
	$interal_type = -1;
	if(strcmp($_GET["type"], "warband")==0)
	{
		$interal_type = 0;
	}
	else if(strcmp($_GET["type"], "wfas")==0)
	{
		$interal_type = 1;
	}
	$s = new ServerInfoFetcher();
	$retJson = $s->FetchAllServerDetailsReturnJson($s->FetchServerIPList(0));
	echo $retJson;
}
?>