<?php 
Include("mb_server_fetch.php");
Include("mb_server_monitor_config.php");
if(isset($_GET["ip"]))
{
	$ip=$_GET["ip"];
	
	$si=ServerInfoFetcher::FetchServerDetailsByIP($ip);
?>
<html>
	<head>
		<title>Server Info on <?php echo $ip ?></title>
	</head>
	<body>
		<table align="center" border="1">
			<tr>
				<td width="300" align="center">IP</td>
				<td width="300" align="center"><?php echo $ip ?></td>
			</tr>
			<tr>
				<td width="300" align="center">Server Name</td>
				<td width="300" align="center"><?php echo $si->{"server_name"} ?></td>
			</tr>
			<tr>
				<td width="300" align="center">Module</td>
				<td width="300" align="center"><?php echo $si->{"server_module"} ?></td>
			</tr>
			<tr>
				<td width="300" align="center">Mode</td>
				<td width="300" align="center"><?php echo $si->{"server_mode"} ?></td>
			</tr>
			<tr>
				<td width="300" align="center">Map</td>
				<td width="300" align="center"><?php echo $si->{"server_map"} ?></td>
			</tr>
			<tr>
				<td width="300" align="center">Players</td>
				<td width="300" align="center"><?php echo $si->{"server_player_nums"} ?>/<?php echo $si->{"server_max_player_nums"} ?></td>
			</tr>
			<tr>
				<td width="300" align="center">HasPassword</td>
				<td width="300" align="center"><?php echo $si->{"isLocked"} ?></td>
			</tr>
		<?php
}
else
{
	echo "Invalid IP Address";
}
			
		?>
		</table>
	</body>
</html>