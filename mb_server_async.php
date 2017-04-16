<?php
Include("mb_server_fetch.php");
Include("mb_server_monitor_config.php");
$server_ip_list=ServerInfoFetcher::FetchServerIPList(1);
$index=0;
$server_detail_array=ServerInfoFetcher::FetchAllServerDetails($server_ip_list);
foreach($server_detail_array as $key=>$current_server_details)
{
	$si=$current_server_details;
	if($si!=null)
	{
		$index++;
		?>
		<tr>
			<td width="20" align="center"><?php echo $index ?></td>
			<td width="150" align="center"><?php echo $si->{"server_ip"} ?></td>
			<td width="300" align="center"><?php echo $si->{"server_name"} ?></td>
			<td width="100" align="center"><?php echo $si->{"server_module"} ?></td>
			<td width="100" align="center"><?php echo $si->{"server_mode"} ?></td>
			<td width="100" align="center"><?php echo $si->{"server_map"} ?></td>
			<td width="50" align="center"><?php echo $si->{"server_player_nums"} ?>/<?php echo $si->{"server_max_player_nums"} ?></td>
			<td width="50" align="center"><?php echo $si->{"isLocked"} ?></td>
		</tr>
<?php
	}
}
?>