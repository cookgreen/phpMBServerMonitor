<?php
require_once("../entity/serverinfo.php");
require_once("Untility.php");
/*
 * @Name: ServerCacher
 * @Description: Cache the data from remote server 
 */
class ServerCacher
{
	private $manifest_file;
	private $fp;
	public function __construct($manifest_file)
	{
		$this->manifest_file = $manifest_file;
	}
	/*
	 * @Name: CacheDataToCSV
	 * @Description: Cache Single data to csv file and record it to manifest file
	 */
	public function CacheDataToCSV($si, $csv, $relativePath, $isRelativeToCacheFolder)
	{	
		if($si instanceof ServerInfo)
		{
			$finalCSV = $relativePath . "/" . $csv;
			$this->fp = fopen($finalCSV, "w");
			if($this->fp!=FALSE)
			{
				$fields = array();
				$fields[0]=$si->server_ip;
				$fields[1]=$si->server_name;
				$fields[2]=$si->server_module;
				$fields[3]=$si->server_mode;
				$fields[4]=$si->server_map;
				$fields[5]=$si->server_player_nums;
				$fields[6]=$si->server_max_player_nums;
				$fields[7]=$si->isLocked;
				if(fputcsv($this->fp, $fields)!=FALSE)
				{
					fclose($this->fp);
					$this->fp = fopen($this->manifest_file, "r");

					$data = file_get_contents($this->manifest_file);
					$content = $si->server_ip . " " . $finalCSV;
					$data .= PHP_EOL . $content;
					file_put_contents($this->manifest_file, $data);
					fclose($this->fp);
				}
			}
		}
	}
	
	public function ClearCache()
	{
		if(!file_exists($this->manifest_file))
		{
			return FALSE;
		}
		$this->fp = fopen($this->manifest_file, "r");
		if($this->fp!=FALSE)
		{
			while(!feof($this->fp)) {
				$line = fgets($this->fp);
				$tokens = explode(" ", $line);
				unlink("'" . $tokens[1] . "'");
			}
		}
		unlink("'" . $this->manifest_file . "'");
		
		$handler = opendir("../cache/");
		while(($filename = readdir($handler)) !== FALSE)
		{
			$extension = Utility::getFileExtension($filename);
			if(strcmp($extension, "csv")==0)
			{
				unlink("../cache/" . $filename);
			}
		}
	}
	
	public function GetCachedCSVByIP($ip)
	{
		$this->fp = fopen($this->manifest_file, "r");
		if($this->fp!=FALSE)
		{
			while(!feof($this->fp)) {
				$line = fgets($this->fp);
				$tokens = explode(" ", $line);
				if(strcmp($tokens[0], $ip)==0)
				{
					return trim($tokens[1], PHP_EOL);
					break;
				}
			}
		}
		return FALSE;
	}
	
	/*
	 * @Name: ReadDataFromCSV
	 * @Description: Read a cached data from csv file
	 */
	public function ReadCacheDataFromCSV($csv)
	{
		$this->fp = fopen($csv, "r");
		if($this->fp!=FALSE)
		{
			$fields = fgetcsv($this->fp);
			if(empty($fields[0]) || $fields[0]==null)
			{
				return;
			}
			$si = new ServerInfo();
			$si->server_ip = $fields[0];
			$si->server_name = $fields[1];
			$si->server_module = $fields[2];
			$si->server_mode = $fields[3];
			$si->server_map = $fields[4];
			$si->server_player_nums = $fields[5];
			$si->server_max_player_nums = $fields[6];
			$si->isLocked = $fields[7];
			fclose($this->fp);
			return $si;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function CheckCacheExist()
	{
		return file_exists($this->manifest_file);
	}
	
	public function CreateCache()
	{
		fopen($this->manifest_file, "w");
	}
}

?>