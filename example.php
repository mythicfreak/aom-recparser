<html>
	<head>
		<title>AoM/AoT Rec Parser - Mythic_Freak</title>
		<style type="text/css">
			table.sample {
				border-width: 1px;
				border-spacing: 0px;
				border-style: solid;
				border-color: black;
				border-collapse: collapse;
				background-color: white;
			}
			table.sample th, table.sample td {
				border-width: 1px;
				padding: 1px;
				border-style: inset;
				border-color: gray;
				background-color: white;
			}
		</style>
	</head>
	<body>
<?php
	if(!isset($_GET["file"])) die("This script requires ?file=path/to/file.rec parameter.");
	require_once("parser.php");
	$parser = new Parser($_GET["file"]);
	
	echo "<div><b>Map: </b>" . $parser->getMapName() . " (" . $parser->getMapSize() . ")</div>";
	echo "<div><b>Date: </b>" . $parser->getGameDate() . "</div>";
	echo "<div><b>Mode: </b>" . $parser->getGameMode() . "</div>";
	echo "<div><b>Type: </b>" . $parser->getGameType() . "</div>";
?>
		<table class="sample">
			<tr>
				<th>Number</th>
				<th>Name</th>
				<th>Rating</th>
				<th>God</th>
				<th>Team</th>
				<th>Type</th>
			</tr>
<?php
	$num = $parser->getNumPlayers();
	for($i=1; $i <= $num; $i++)
	{
		$name = $parser->getPlayerName($i);
		$rate = $parser->getPlayerRating($name);
		$god  = $parser->getPlayerGod($name);
		$team = $parser->getCombinedPlayerTeam($name);
		$type = $parser->getPlayerType($name);
		echo "<tr><td>$i</td><td>$name</td><td>$rate</td><td>$god</td><td>$team</td><td>$type</td></tr>";
	}
?>
		</table>
		
		<p>
			Other examples:
			<ul>
				<li><a href="?file=recs/aom.rec">aom.rec</a></li>
				<li><a href="?file=recs/aom2.rec">aom2.rec</a></li>
				<li><a href="?file=recs/aom3.rec">aom3.rec</a></li>
				<li><a href="?file=recs/aom4.rec">aom4.rec</a></li>
				<li><a href="?file=recs/sup.rcx">supremacy</a></li>
				<li><a href="?file=recs/dmv.rcx">deathmatch</a></li>
				<li><a href="?file=recs/tiny.rcx">tiny</a></li>
				<li><a href="?file=recs/zelda.rcx">zelda</a></li>
			</ul>
		</p>
	</body>
</html>