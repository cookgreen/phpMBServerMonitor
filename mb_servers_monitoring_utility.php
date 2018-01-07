<?
class Utility
{
	static function Ping($address)
	{
		$ping = exec('ping '. $address);
		$chunks = explode(' ', $ping);
		$avg = substr($chunks[10], 0, strlen($chunks[10])-2);
		return $avg;
	}
}
?>