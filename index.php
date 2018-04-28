<html>
	<head>
		<title>Mount & Blade Server Monitor - Index</title>
		<script type="text/javascript" src="js/jquery-1.8.2.js"></script>
		<script type="text/javascript" src="http://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
		<link rel="stylesheet" href="http://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
		<script>
			$(document).ready(function() {
				$('#tbServerLst').DataTable( {
					"ajax":{
						"url":"service/serverfetcher.php?type=warband",
						"dataSrc":""
					},
					"columns": [
						{ "data": "server_ip" },
						{ "data": "server_name" },
						{ "data": "server_module" },
						{ "data": "server_mode" },
						{ "data": "server_map" },
						{ "data": "server_player_nums" },
						{ "data": "isLocked" }
					]
				} );
			} );
		</script>
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
				<span>Mount Blade Server Monitor</span>
			</div>
			<div class="mainpage">
				<table id="tbServerLst" class="display cell-border">
					<thead>
						<tr class="table_head">
							<th>IP</th>
							<th>Server Name</th>
							<th>Module</th>
							<th>Mode</th>
							<th>Map</th>
							<th>Players</th>
							<th>HasPassword</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div> 
	</body>
</html>