<?php 
require_once("mb_server_fetch.php");
require_once("mb_server_monitor_config.php");

?>
<html>
	<head>
		<title>Mount & Blade Server Monitor - Index</title>
		<script type="text/javascript" src="js/jquery-1.8.2.js"></script>
		<script type="text/javascript" src="js/table.js"></script>
		<link rel="stylesheet" href="css/table.css" />
		<link rel="stylesheet" href="css/common.css" />
		<style>
		html{
			height: auto;
		}
		body{
			height: auto;
		}
		.page_containter{
			width: 100%;
			height: auto;
		}
		.header{
			width: 1000px;
			margin: 0 auto;
			text-align: center;
			font-size: 15pt;
		}
		.mainpage{
			width: 1000px;
			margin: 0 auto;
			height: auto;
			padding: 10px;
		}
		</style>
	</head>
	<body>
		<div class="page_containter">
			<div class="header">
				<span>Mount & Blade Server Monitor</span>
			</div>
			<div class="mainpage">
				<table class="tbServerLst" align="center" border="1">
					<tr class="table_head">
						<td width="150" align="center">IP</td>
						<td width="300" align="center">Server Name</td>
						<td width="100" align="center">Module</td>
						<td width="100" align="center">Mode</td>
						<td width="100" align="center">Map</td>
						<td width="50" align="center">Players</td>
						<td width="50" align="center">HasPassword</td>
						<td width="100" align="center">Details</td>
					</tr>
					<?php
						$server_ip_list=ServerInfoFetcher::FetchServerIPList(0);
						ServerInfoFetcher::FetchAllServerDetails($server_ip_list);
					?>
				</table>
			</div>
		</div> 
	</body>
</html>