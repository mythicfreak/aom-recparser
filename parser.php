<?php
	class Parser
	{
		private $expansion; //AoM or AoT?
		private $xml; //embedded XML data
		private $rms = ""; //embedded Random Map Script
		private $playerGods = array(); //actual player gods
		private $playerTeams = array(); //actual player teams
			
		public /*Parser*/ function __construct($filePath)
		{
			$length = strlen($filePath);
			$ext = substr($filePath, $length-3, 3);
			if($ext == 'rec')
				$this->expansion = false;
			else if($ext == 'rcx')
				$this->expansion = true;
			else
				throw new Exception("Invalid file extension.");
			
			$this->parse($filePath);
		}
		
		public /*SimpleXMLElement*/ function getXml()
		{
			return $this->xml;
		}
		
		public /*string*/ function getRandomMapScript()
		{
			return $this->rms;
		}
		
		public /*string*/ function getGameType()
		{
			//$type = $this->xml->xpath("/GameSettings/GameType");
			//echo $type[0];
			
			if($this->expansion)
				return "AoT";
			else
				return "AoM";
		}
		
		public /*string*/ function getMapName()
		{
			$map = $this->xml->xpath("/GameSettings/Filename");
			return $map[0];
		}
		
		public /*int*/ function getPointOfView() //returns the player who recorded this
		{
			$pov = $this->xml->xpath("/GameSettings/CurrentPlayer");
			return $pov[0];
		}
		
		public /*int*/ function getTimeStamp()
		{
			$start = $this->xml->xpath("/GameSettings/GameStartTime");
			$start = $start[0];
			return (int)$start;
		}
		
		public /*string*/ function getGameDate() //not sure about time zone, probably GMT-7'ish
		{
			return date('Y-m-d H:i:s', $this->getTimeStamp()); //http://php.net/manual/en/function.date.php
		}
		
		public /*string*/ function getMapSize()
		{
			$size = $this->xml->xpath("/GameSettings/MapSize");
			$size = $size[0];
			$size = (int)$size;
			if($size == 0)
				return "Normal Map";
			else
				return "Large Map";
		}
		
		public /*string*/ function getGameMode()
		{
			$gameModeID = $this->xml->xpath("/GameSettings/GameMode");
			return $this->getGameModeName($gameModeID[0]);
		}
		
		public /*int*/ function getMapSeed()
		{
			$seed = $this->xml->xpath("/GameSettings/MapSeed");
			return (int)$seed[0];
		}
		
		public /*int*/ function getNumPlayers() //excluding mother nature
		{
			$num = $this->xml->xpath("/GameSettings/Player");
			return count($num);
		}
		
		public /*array(string)*/ function getAllPlayers()
		{
			$players = $this->xml->xpath("/GameSettings/Player/Name");
			$num = count($players);
			$result = array();
			for($i=0; $i < $num; $i++)
				$result[$i] = (string)$players[$i];
			
			return $result;
		}
		
		public /*float*/ function getPlayerRating($playerName)
		{
			$query = "/GameSettings/Player[Name='" . $playerName . "']/Rating";
			$rate = $this->xml->xpath($query);
			return $rate[0];
		}
		
		public /*string*/ function getPlayerType($playerName)
		{
			$query = "/GameSettings/Player[Name='" . $playerName . "']/Type";
			$type = $this->xml->xpath($query);
			return $this->getTypeName($type[0]);
		}
		
		public /*int*/ function getChosenPlayerTeam($playerName) //might be 255 = random
		{
			$query = "/GameSettings/Player[Name='" . $playerName . "']/Team";
			$team = $this->xml->xpath($query);
			return $team[0];
		}
		
		public /*int*/ function getPlayerTeam($playerName) //returns actual player team
		{
			$playerID = $this->getPlayerNumber($playerName);
			return $this->playerTeams[$playerID];
		}
		
		public /*string*/ function getCombinedPlayerTeam($playerName) //shows if the teams were random
		{
			$team = $this->getChosenPlayerTeam($playerName);
			$playerID = $this->getPlayerNumber($playerName);
			$team2 = $this->playerTeams[$playerID];
			
			if($team == 255) //random
				return $team2 . " (Random)";
			else
				return $team2;
		}
		
		public /*array(string)*/ function getPlayersOnTeam($team) //based on actual team
		{
			$num = $this->getNumPlayers();
			$results = array();
			for($i=0; $i < $num; $i++)
				if($this->playerTeams[$i] == $team)
					array_push($results, $this->getPlayerName($i));
			
			return $results;
		}
		
		public /*int*/ function getNumTeams()
		{
			return max($this->playerTeams);
		}
		
		public /*array(int)*/ function getAllTeams()
		{
			return array_unique($this->playerTeams);
		}
		
		public /*int*/ function getPlayerGodID($playerName) //returns ID of actual god
		{
			$playerID = $this->getPlayerNumber($playerName);
			return $this->playerGods[$playerID]; //actual god
		}
		
		public /*string*/ function getPlayerGod($playerName) //returns name of god (combined chosen + actual)
		{
			$playerID = $this->getPlayerNumber($playerName);
			$query = "/GameSettings/Player[Name='" . $playerName . "']/Civilization";
			$god = $this->xml->xpath($query);
			$god = $this->getGodName($god[0]); //chosen god
			$god2 = $this->getGodName($this->playerGods[$playerID]); //actual god
			if($god == $god2)
				return $god;
			else //random god
				return $god2 . " (" . $god . ")";
		}
		
		public /*int*/ function getPlayerNumber($playerName) //[1, n] (mother nature = 0)
		{
			$query = "/GameSettings/Player[Name='" . $playerName . "']/@ClientID";
			$id = $this->xml->xpath($query);
			return $id[0] + 1;
		}
		
		public /*string*/ function getPlayerName($playerNumber)
		{
			$query = "/GameSettings/Player[@ClientID='" . ($playerNumber - 1) . "']/Name";
			$name = $this->xml->xpath($query);
			return $name[0];
		}
		
		private /*string*/ function getGameModeName($gameModeID)
		{
			switch($gameModeID)
			{
				case  0: return "Supremacy";
				case  1: return "Conquest"; //double-check
				case  2: return "Lightning"; //double-check
				case  3: return "Deathmatch";
				case  4: return "Scenario"; //double-check
				default: return "Unknown Game Type: " . $gameModeID;
			}
		}
		
		private /*string*/ function getTypeName($entityTypeID)
		{
			switch($entityTypeID)
			{
				case  0: return "Human";
				case  1: return "AI";
				case  4: return "Observer";
				default: return "Unknown Entity Type: " . $entityType;
			}
		}
		
		private /*string*/ function getVanillaGodName($godID)
		{
			switch($godID)
			{
				case  0: return "Zeus";
				case  1: return "Poseidon";
				case  2: return "Hades";
				case  3: return "Isis";
				case  4: return "Ra";
				case  5: return "Set";
				case  6: return "Odin";
				case  7: return "Thor";
				case  8: return "Loki";
				case  9: return "Random All";
				case 10: return "Random Greek";
				case 11: return "Random Norse";
				case 12: return "Random Egyptian";
				case 13: return "Nature";
				default: return "Unknown God: " . $godID;
			}
		}
		
		private /*string*/ function getExpansionGodName($godID)
		{
			switch($godID)
			{
				case  0: return "Zeus";
				case  1: return "Poseidon";
				case  2: return "Hades";
				case  3: return "Isis";
				case  4: return "Ra";
				case  5: return "Set";
				case  6: return "Odin";
				case  7: return "Thor";
				case  8: return "Loki";
				case  9: return "Kronos";
				case 10: return "Oranos";
				case 11: return "Gaia";
				case 12: return "Random All";
				case 13: return "Random Greek";
				case 14: return "Random Norse";
				case 15: return "Random Egyptian";
				case 16: return "Random Atlantean";
				case 17: return "Nature";
				default: return "Unknown God: " . $godID;
			}
		}
		
		private /*string*/ function getGodName($godID)
		{
			if($this->expansion)
				return $this->getExpansionGodName($godID);
			else
				return $this->getVanillaGodName($godID);
		}
		
		private /*void*/ function parse($filePath)
		{
			//load file
			$handle = fopen($filePath, "r");
			if(!$handle) throw new Exception("Could not open file for reading.");
			$leet = fread($handle, 4); if($leet != "l33t") throw new Exception("Invalid rec file.");
			$size = unpack("l", fread($handle, 4)); $size = $size[1];
			$compressed = fread($handle, filesize($filePath)-8);
			fclose($handle);
			
			//decompress
			$data = gzuncompress($compressed);
			if($size != strlen($data)) throw new Exception("Length check failed.");
			
			//$handle = fopen("test.tmp", "w") or die("Could not create temp file.");
			//fwrite($handle, $data);
			//fclose($handle);
			
			//read XML sizes
			if($this->expansion)
				$seek = 1470;
			else
				$seek = 1430;
			$totalSize = unpack("l", substr($data, $seek-4, 4)); $totalSize = $totalSize[1];
			$blockSize = unpack("l", substr($data, $seek, 4)); $blockSize = $blockSize[1];
			
			//start processing blocks of XML data
			$xml = "";
			$seek += 4;
			while ($totalSize > 0)
			{
			    $toRead = min($totalSize, $blockSize);
			    $txt = substr($data, $seek, $toRead); //unicode?
			    $xml .= $txt;
			    $seek += $toRead + 4;
			    $totalSize -= $toRead;
			}
			
			//save XML
			$this->xml = new SimpleXMLElement($xml);
			
			//read RMS sizes (unpack "l": signed long,always 32 bit, machine byte order)
			$totalSize = unpack("l", substr($data, $seek-4, 4)); $totalSize = $totalSize[1];
			$blockSize = unpack("l", substr($data, $seek, 4)); $blockSize = $blockSize[1];
			
			//start processing blocks of RMS data
			$rms = "";
			$seek += 4;
			while ($totalSize > 0)
			{
			    $toRead = min($totalSize, $blockSize);
			    $txt = substr($data, $seek, $toRead);
			    $rms .= $txt;
			    $seek += $toRead + 4;
			    $totalSize -= $toRead;
			}
			
			//save RMS
			$this->rms = $rms;
			
			//parse actual civs/teams
			$totalSize = unpack("l", substr($data, $seek-4, 4)); $totalSize = $totalSize[1]; //cNumPlayers
			for ($i=0; $i < $totalSize; $i++)
			{
				$playerCiv = unpack("l", substr($data, $seek, 4)); $playerCiv = $playerCiv[1];
				$playerTeam = unpack("l", substr($data, $seek+4, 4)); $playerTeam = $playerTeam[1];
				$seek += 8;
			    
			    $this->playerGods[$i] = $playerCiv;
			    $this->playerTeams[$i] = $playerTeam;
			}
		}
	}
?>