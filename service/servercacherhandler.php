<?php
require_once("../helper/servercacher.php");
require_once("../entity/resultmessage.php");
if(isset($_POST["action"]))
{
	$action = $_POST["action"];
	if(strcmp($action, "clear_cache")==0)
	{
		$msg = new ResultMessage();
		$s = new ServerCacher("../cache/serverlist.manifest");
		$ret = $s->ClearCache();
		if($ret==FALSE)
		{
			$msg->status="Failed";
			$msg->description="Clear cache failed, cached file may not exist!";
			$msg->data = "";
		}
		else
		{
			$msg->status="OK";
			$msg->description="Clear cache finished!";
			$msg->data = "";
		}
		echo json_encode($msg);
	}
}
?>