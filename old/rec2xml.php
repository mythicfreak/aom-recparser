<?php	
	function cleanstr($str)
	{
		$str = preg_replace('/[^a-zA-Z0-9!<>="_. \/\-]/', '', $str);
		return $str;
	}
	
	//upload file
	if( ($_FILES["file"]["type"] == "application/octet-stream") && 
		($_FILES["file"]["size"] < 500*1024))
	{
		if ($_FILES["file"]["error"] > 0) {
			die("Upload error: " . $_FILES["file"]["error"] . "<br />");
		} else {
			$ext = substr($_FILES["file"]["name"], strlen($_FILES["file"]["name"])-4);
			if($ext !== ".rec" && $ext !== ".rcx") die("Invalid extension");
			if(substr($_FILES["file"]["name"], 0, 14) == "Recorded Game ") die("Please rename the file before you upload it.");
		}
		$target_path = "uploads/" . basename($_FILES['file']['name']);
		if (!file_exists($target_path)) { //if it already exists code below will work just fine, else move the temp file there
    		move_uploaded_file($_FILES['file']['tmp_name'], $target_path) or die("Can't upload file");
      	}
	}
	else
		die("Invalid file, or too big (max 500Kb)");

	$filename = $target_path;
	$handle = fopen($filename, "r") or die("Can't open read file");
	$head = fread($handle, 8);
	$contents = fread($handle, filesize($filename)-8);
	fclose($handle);
	
	$res = gzuncompress($contents);
	$res = cleanstr($res);
	
	$start = strrpos($res, "<GameSettings>", 0);
	$stop = strrpos($res, "</GameSettings>", 0);
	$xml = substr($res, $start, $stop-$start); //+15
	
	header ("content-type: text/xml");
	echo "<?xml version=\"1.0\"?>";
	echo "<?xml-stylesheet type=\"text/xsl\" href=\"recs.xsl\"?>";
	echo $xml;
	echo "<Winner>Watch and see!</Winner>";
	preg_match("/<GameStartTime>(.*)<\/GameStartTime>/", $xml, $matches);
	echo "<Date>" . date("D Y/m/d - H\hi", $matches[1]) . "</Date>";
	echo "</GameSettings>";
?>