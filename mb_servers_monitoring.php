<?php 
Include("mb_server_fetch.php");
Include("mb_server_monitor_config.php");

?>
<html>
	<head>
		<title>Mount & Blade Server Monitor</title>
	</head>
	<body>
		<table align="center" border="1">
			<tr>
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
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER["REQUEST_URI"]) . "/mb_server_async.php");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);
			 
			curl_exec($ch);
			curl_close($ch);
		?>
		</table>
	</body>
</html>