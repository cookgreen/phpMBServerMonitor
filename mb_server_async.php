<?php
Include("mb_servers_fetch.php");
$server_ip_list=ServerInfoFetcher::FetchServerIPList(1);
$index=0;
foreach($server_ip_list as $current_ip)
{
	$si=ServerInfoFetcher::FetchServerDetailsByIP($current_ip);
	if($si!=null)
	{
		$index++;
		?>
		<tr>
			<td width="20" align="center"><?php echo $index ?></td>
			<td width="150" align="center"><?php echo $current_ip ?></td>
			<td width="300" align="center"><?php echo $si->{"server_name"} ?></td>
			<td width="100" align="center"><?php echo $si->{"server_module"} ?></td>
			<td width="100" align="center"><?php echo $si->{"server_mode"} ?></td>
			<td width="100" align="center"><?php echo $si->{"server_map"} ?></td>
			<td width="50" align="center"><?php echo $si->{"server_player_nums"} ?></td>
			<td width="50" align="center"><?php echo $si->{"isLocked"} ?></td>
			<td width="50" align="center"><?php echo $si->{"isLocked"} ?></td>
		</tr>
<?php
	}
}
?>