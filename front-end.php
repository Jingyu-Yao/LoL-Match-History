<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>LoL match history</title>
		<meta name="keywords" content="League of Legends match history">
		<meta name="description" content="A small test project using Riot's API">
		<meta name="author" content="Jingyu Yao">
		<!--[if lt IE 9]>
		<script src="/scripts/html5shiv.js"></script>
		<![endif]-->
		<style>
			table.db-table {
				width: 100%;
				border-right: 1px solid #ccc;
				border-bottom: 1px solid #ccc;
			}
			table.db-table th {
				background: #eee;
				padding: 5px;
				border-left: 1px solid #ccc;
				border-top: 1px solid #ccc;
			}
			table.db-table td {
				padding: 5px;
				border-left: 1px solid #ccc;
				border-top: 1px solid #ccc;
				text-align: center;
				vertical-align: middle;
			}
		</style>
	</head>
	<body>
		<?php include_once("../scripts/analyticstracking.php") ?>
		<?php
		// session_start(); //remember this has to be first line of code if we want to use it
		#ini_set('display_startup_errors', 1);
		#ini_set('display_errors', 1);
		#error_reporting(-1);
		require_once ('lolmethods.php');

		//this part needs to be here so the form will work
		$name = empty($_GET["name"]) ? NULL : $_GET["name"];
		?>

		<form name="summonerform" method="get">
			<p>
				Enter a summoner name:
				<br>
				<input type="text" name="name" value = "<?php echo $name; ?>">
				<br>
				<input type="submit" value="submit">
			</p>
		</form>

		<?php
		if ($name == NULL) {
			exit("Type in something...");
		}
		//cleaning up user input of weird character and spaces
		$name = preg_replace("/[^a-zA-Z0-9]+/", "", $name);
		if ($name == "") {
			exit("Type in something...");
		}

		$fromCache = false;
		// cacheing code
		switch(checkCache($name)) {
			case 1 :
				$result = getSummonerByName($name);
				if (gettype($result) != "boolean") {
					cachePlayer($name, $result);
				} else {
					exit ;
				}
				// echo "new cache";
				break;
			case 2 :
				$result = current(getSummonerByName($name));
				updateCache($name, $result);
				// echo "update cache";
				break;
			case 3 :
				$result = getFromCache($name);
				// echo "get from cache";
				break;
			default :
				$result = current(getSummonerByName($name));
				cachePlayer($name, $result);
				break;
		}
		//at this point we already made sure $name exits
		$result = current(getSummonerByName($name));
		if ($result == false) {
			exit ;
		}

		echo "Summoner name: " . $result["name"] . "<br>";
		echo "Level: " . $result['summonerLevel'] . "<br>";
		$recent = getRecentGames($result["id"]);
		$recent = $recent["games"];

		$infoArray = array();
		$idList = array();
		$fellowPlayerList = array();
		echo "<br> Recent games: <br>";
		foreach ($recent as $gameNum => $g) {
			addCell($gameNum + 1, "Game", $gameNum);
			addCell(translateGameType($g["subType"]), "Mode", $gameNum);
			addCell(date('F j, Y, g:i a', $g['createDate'] / 1000), "Time", $gameNum);
			addCell($g["ipEarned"], "IP", $gameNum);
			addCell(($g["teamId"] == 100 ? "Blue" : "Purple"), "Team", $gameNum);
			addCell(getImage($g["championId"], "champion"), "Champion", $gameNum);
			addCell(getImage($g["spell1"], "spell"), "Spell 1", $gameNum);
			addCell(getImage($g["spell2"], "spell"), "Spell 2", $gameNum);

			foreach ($g["stats"] as $key => $value) {
				switch($key) {
					case (preg_match("/item/", $key) ? true : false) :
						// addCell(code2name($value, "items"), $key, $gameNum);
						addCell(getImage($value, "item"), $key, $gameNum);
						break;
					case (preg_match("/damage/i", $key) ? true : false) :
					case (preg_match("/gold/i", $key) ? true : false) :
					case "totalHeal" :
						addCell(round($value / 1000, 1) . "k", $key, $gameNum);
						break;
					case "timePlayed" :
						addCell(round($value / 60, 0) . "min", $key, $gameNum);
						break;
					case "totalTimeCrowdControlDealt" :
						addCell($value . "s", $key, $gameNum);
						break;
					case "team" :
						break;
					case "win" :
						if ($value == true) {
							addCell("yes", $key, $gameNum);
						} else {
							addCell("no", $key, $gameNum);
						}
						break;
					default :
						addCell($value, $key, $gameNum);
						break;
				}
			}

			// group the names together for combined call to the api
			if (!empty($g["fellowPlayers"])) {
				$fellowPlayerList[] = $g["fellowPlayers"];
				foreach ($g["fellowPlayers"] as $p) {
					$idList[] = $p["summonerId"];
				}
			}
		}// end of foreach in recent

		// in order to minimize calls, check summoner list from my db first
		// if exist, drop from the list to retrieve from the server
		foreach ($idList as $key => $id) {
			if (checkSummonerFromDB($id) != false) {
				unset($idList[$key]);
			}
		}

		// make combined call to the api
		$chunked = array_chunk($idList, 40);
		$names = array();
		foreach ($chunked as $k) {
			$names[] = getSummonerByID(implode(",", $k));
		}

		// combine the results
		$allNames = array();
		if (!empty($names)) {
			foreach ($names as $n) {
				$allNames = $allNames + $n;
			}
		}
		
		$hrefBase = "http://www.jingyuyao.com/league.php?name=";
		// now we have the names lets put them out in the cells
		foreach ($fellowPlayerList as $key => $list) {
			foreach ($list as $key2 => $p) {
				if (empty($allNames[$p["summonerId"]]["name"])) {
					$name = checkSummonerFromDB($p["summonerId"]);
				} else {
					$name = $allNames[$p["summonerId"]]["name"];
				}
				addCell(getImage($p["championId"], "champion") . "<br>" . ($p["teamId"] == 100 ? "<a href='" . $hrefBase . htmlentities($name) . "' style='color: blue'>" . $name . "</a>" : "<a style='color: purple' href='" . $hrefBase . htmlentities($name) . "'>" . $name . "</a>"), "Player " . ($key2 + 1), $key);
				addSummonertoDB($p["summonerId"], $name);
			}
		}

		//display the information
		$list = array("Game" => "Game", "Mode" => "Mode", "Time" => "Time", "IP" => "IP", "Team" => "Team", "Champion" => "Champion", "Spell 1" => "Spell 1", "Spell 2" => "Spell 2", 'timePlayed' => "Time played", 'level' => "Level", 'championsKilled' => "Kills", 'numDeaths' => "Death", 'assists' => "Assists", 'minionsKilled' => "CS", 'goldEarned' => "Gold earned", 'item0' => "Item 1", 'item1' => "Item 2", 'item2' => "Item 3", 'item3' => "Item 4", 'item4' => "Item 5", 'item5' => "Item 6", 'item6' => "Trinket", 'physicalDamageDealtPlayer' => "Total phy. dealt", 'physicalDamageDealtToChampions' => "Phy. X champ", 'magicDamageDealtPlayer' => "Total magic dealt", 'magicDamageDealtToChampions' => "Magic X champ", 'trueDamageDealtPlayer' => "True damage dealt", 'trueDamageDealtToChampions' => "True damage X champ", 'totalDamageDealt' => "Damage dealt", 'totalDamageDealtToChampions' => "Total X champ", 'largestCriticalStrike' => "Largest crit.", "totalTimeCrowdControlDealt" => "CC dealt", 'totalDamageTaken' => "Damage taken", 'physicalDamageTaken' => "Phy. taken", 'magicDamageTaken' => "Magic taken", 'trueDamageTaken' => "True damage taken", 'totalHeal' => "Total heal", 'totalUnitsHealed' => "# units healed", 'neutralMinionsKilled' => "Neutral minions", 'neutralMinionsKilledYourJungle' => "Own jungle", 'neutralMinionsKilledEnemyJungle' => "Jungle steal", 'wardKilled' => "Ward killed", 'wardPlaced' => "Ward placed", 'sightWardsBought' => "Sight wards brought", 'visionWardsBought' => "Vision wards brought", 'turretsKilled' => "Turrets", 'barracksKilled' => "Inhibs", 'killingSprees' => "Sprees", 'largestKillingSpree' => "Largest spree", 'largestMultiKill' => "Largest multi-kill", 'doubleKills' => "Double kill", 'tripleKills' => "Triple kill", 'quadraKills' => "Quadra kill", 'pentaKills' => "Penta kill", 'Player 1' => "Other players", 'Player 2' => "", 'Player 3' => "", 'Player 4' => "", 'Player 5' => "", 'Player 6' => "", 'Player 7' => "", 'Player 8' => "", 'Player 9' => "");

		echo '<table cellpadding="0" cellspacing="0" class="db-table">';
		echo "<tr>";
		foreach ($list as $l) {
			echo "<th>" . $l . "</th>";
		}
		echo "</tr>";
		foreach ($infoArray as $i) {
			if ($i["Game"] == 6) {
				echo "<tr>";
				foreach ($list as $l) {
					echo "<th>" . $l . "</th>";
				}
				echo "</tr>";
			}
			if ($i["win"] == "yes") {
				echo "<tr style='background-color:#ADEBAD'>";
			} else {
				echo "<tr style='background-color:#FF9999'>";
			}
			foreach ($list as $key => $name) {
				if (empty($i[$key]) || $i[$key] == null) {
					echo "<td> --- </td>";
				} else {
					switch($key) {
						case "Team" :
							if ($i[$key] == "Blue") {
								echo "<td style='background-color:blue'></td>";
							} else {
								echo "<td style='background-color:purple'></td>";
							}
							break;
						default :
							echo "<td>" . $i[$key] . "</td>";
							break;
					}

				}

			}
			echo "</tr>";
		}
		echo "</table>";

		//infoArray is the 2d representation of the data
		function addCell($var, $key, $num) {
			global $infoArray;
			if (array_key_exists($num + 1, $infoArray)) {
				$infoArray[$num + 1][$key] = $var;
			} else {
				$infoArray[$num + 1][] = $key;
				$infoArray[$num + 1][$key] = $var;
			}
		}

		function translate($var) {
			global $list;
			if (!empty($list[$var])) {
				return $list[$var];
			}
			return $var;
		}

		function translateGameType($var) {
			$types = array('NONE' => 'Custom game', 'NORMAL' => 'SR unranked', 'NORMAL_3x3' => 'TTree unranked', 'ODIN_UNRANKED' => 'Dominion', 'ARAM_UNRANKED_5x5' => 'ARAM', 'BOT' => 'Norm bot', 'BOT_3x3' => 'TTree bot', 'RANKED_SOLO_5x5' => 'SR ranked solo', 'RANKED_TEAM_3x3' => 'TTree ranked team', 'RANKED_TEAM_5x5' => 'SR ranked team', 'ONEFORALL_5x5' => 'One for All', 'FIRSTBLOOD_1x1' => 'Snowdown 1x1', 'FIRSTBLOOD_2x2' => 'Snowdown 2x2', 'SR_6x6' => 'Hexakill', 'CAP_5x5' => 'Team Builder', 'URF' => 'URF', 'URF_BOT' => 'URF bot', 'NIGHTMARE_BOT' => 'Nightmare bot', );
			return $types[$var];
		}

		// foreach ($infoArray as $key => $value) {
		// echo $key . "<br>";
		// }
		// echo "<pre>";
		// echo print_r($infoArray);
		// echo "</pre>";
		?>
	</body>
</html>
