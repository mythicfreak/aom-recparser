<html>
	<head>
		<title>AoM/AoT Rec Parser - Mythic_Freak</title>
	</head>
	<body>

<?php
	if(!isset($_GET["file"])) die("This script requires ?file=path/to/file.rec parameter.");
	require_once("parser.php");
	$parser = new Parser($_GET["file"]);
	header ("content-type: text/html"); //default
	echo "<pre>" . htmlspecialchars($parser->getRandomMapScript()) . "</pre>";
?>

	</body>
</html>