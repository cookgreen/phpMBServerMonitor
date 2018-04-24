<?php
Include("mb_server_fetch.php");
Include("mb_server_monitor_config.php");
$server_ip_list=ServerInfoFetcher::FetchServerIPList(0);
ServerInfoFetcher::FetchAllServerDetails($server_ip_list);
?>