<?php
require_once("../helper/servercacher.php");
require_once("../helper/untility.php");
require_once("../entity/resultmessage.php");
require_once("../entity/pagecounter.php");
require_once("../entity/serverinfo.php");
require_once("../config/config.php");

header('Access-Control-Allow-Origin:*');


class ServerInfoFetcher
{
	private $cacher;
	
	public function __construct()
	{
		$this->cacher = new ServerCacher("../cache/serverlist.manifest");
	}
	
	/*
	 * @Name: FetchServerIPList
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
	 * @Name: FetchServerIPListByPage
	 * @Description: Fetch Server List from official master server
	 * @Input: Game Type; 0 - Mount&Blade Warband; 1 - Mount&Blade With Fire and Sword
	 * @Output: Server Information Object
	 */
	public function FetchServerIPListByPage($type, $page)
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
				
				$displayNum = $GLOBALS["display_num"];
				$pageCount = ceil(count($server_ip_array) / $displayNum);
				$server_ip_array = array_slice($server_ip_array, ($page-1)*$displayNum, $displayNum);
				
				$pagedList = new PageCounter();
				$pagedList->current_number = $page;
				$pagedList->total_number = $pageCount;
				$pagedList->paged_data = $server_ip_array;
				
				return $pagedList;
			}
		}
		catch(Exception $e)
		{
			return FALSE;
		}
	}
	
	/*
	 * @Name: FetchXMLData
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
	 * @Name: FetchServerDetails
	 * @Description: Fetch Server Details Information by various ip addresses
	 * @Input: Ip Array
	 * @Output Server Info Object Array
	 */
	public function FetchServerDetails($ip_array)
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
			
			if($this->cacher->CheckCacheExist())
			{
				$index = 0;
				for($i = 0;$i < $count;$i++)
				{
					$ip = $ip_array[$i];
					$si = $this->cacher->ReadCacheDataFromCSV($this->cacher->GetCachedCSVByIP($ip));
					if($si==FALSE)
					{
						continue;						
					}
					else
					{
						$server_info_array[$index] = $si;
						$index++;
					}
				}
			}
			else
			{
				$this->cacher->CreateCache();	
				
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
								$this->cacher->CacheDataToCSV($si, Utility::guid() . ".csv", "../cache", 1);
								$cur_idx++;
							}
							else
							{
								continue;
							}
						} 
					}
				}while($running > 0);
			}
			
			return $server_info_array;
		}
		catch(Exception $e)
		{
			return FALSE;
		}
	}
	
	/*
	 * @Name: FetchServerDetailsByIP
	 * @Description: Fetch Server Information by specific ip address
	 * @Input: Ip
	 * @Output: Server Information Object
	 */
	public function FetchServerDetailsByIP($ip)
	{
		if($ip!=null)
		{
			$ip_array = array();
			$ip_array[0] = $ip;
			return $this->FetchServerDetails($ip_array)[0];
		}
	}
	
	/*
	 * @Name: FetchAllServerDetailsReturnJson
	 * @Description: Fetch All Server Information and return Json format
	 * @Input: Ip Array
	 * @Output: Json Data Format
	 */
	public function FetchAllServerDetailsReturnJson($ip_array)
	{
		return json_encode($this->FetchServerDetails($ip_array));
	}

	
	/*
	 * @Name: FetchAllServerDetailsReturnPagedJson
	 * @Description: Fetch Paged Server Information and return Json format
	 * @Input: Ip Array
	 * @Output: Json Data Format
	 */
	public function FetchAllServerDetailsReturnPagedJson($ip_array, $totalPage, $currentPage)
	{
		try
		{
			$resultMsg = new ResultMessage();
			$pageCounter = new PageCounter();
			$pageCounter->current_number = $currentPage;
			$pageCounter->total_number = $totalPage;
			
			$server_info_array=$this->FetchServerDetails($ip_array);
			
			$pageCounter->paged_data = $server_info_array;
			$resultMsg->status="OK";
			$resultMsg->description="Fetch Data finished";
			$resultMsg->data = $pageCounter;
			
			return json_encode($resultMsg);
		}
		catch(Exception $e)
		{
			return FALSE;
		}
	}
}

if(isset($_GET["type"]))
{
	$s = new ServerInfoFetcher();
	$interal_type = -1;
	if(strcmp($_GET["type"], "warband")==0)
	{
		$interal_type = 0;
	}
	else if(strcmp($_GET["type"], "wfas")==0)
	{
		$interal_type = 1;
	}
	
	if(isset($_GET["action"]))
	{
		$action = $_GET["action"];
		if(strcmp($action, "get_number")==0)
		{
			echo count($s->FetchServerIPList($interal_type));
		}
	}
	if(isset($_GET["page"]))
	{
		$page = $_GET["page"];
		$pagedList = $s->FetchServerIPListByPage($interal_type, $page);
		$retJson = $s->FetchAllServerDetailsReturnPagedJson($pagedList->paged_data, $pagedList->total_number,  $page);
	}
	else
	{		
		$retJson = $s->FetchAllServerDetailsReturnJson($s->FetchServerIPList($interal_type));
	}
	echo $retJson;
}
?>