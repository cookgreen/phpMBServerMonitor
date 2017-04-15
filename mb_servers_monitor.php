<?php 
Include("mb_servers_fetch.php");
?>
<html>
	<head>
		<title>Mount & Blade Server Monitor</title>
	</head>
	<body>
		<table align="center" border="1">
			<!--<th span="4" align="center">Server Monitor</th>-->
			<tr>
				<td width="20" align="center">No</td>
				<td width="150" align="center">IP</td>
				<td width="300" align="center">Server Name</td>
				<td width="100" align="center">Module</td>
				<td width="100" align="center">Mode</td>
				<td width="100" align="center">Map</td>
				<td width="50" align="center">Players</td>
				<td width="50" align="center">Info</td>
			</tr>
		<?php
			//ServerInfoFetcher::FetchServerDetailsByIPTest("151.80.28.27:7245");
			
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,"http://localhost/mbservermonitor/mb_server_async.php");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);
			//curl_setopt($ch,CURLOPT_TIMEOUT,10);
			 
			curl_exec($ch);
			 
			curl_close($ch);
		?>
		</table>
	</body>
</html>