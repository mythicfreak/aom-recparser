<?php
	if(!isset($_GET["file"])) die("This script requires ?file=path/to/file.rec parameter.");
	require_once("parser.php");
	header ("content-type: text/xml");
	$parser = new Parser($_GET["file"]);
	echo $parser->getXml()->asXML();
?>