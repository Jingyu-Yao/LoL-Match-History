<?php
	// ini_set('display_startup_errors',1);
	// ini_set('display_errors',1);
	// error_reporting(-1);
	
	define("KEY", "?api_key=get your own at https://developer.riotgames.com/");
	define("REGION", "na");
	define("BASE", "https://" . REGION . ".api.pvp.net/api/lol/" . REGION);
	define("WAIT_TIME", 180);
	$myID = 28918769;
	$myName = "holydestroyer";
	
	function getIt($var){
		$json = @file_get_contents($var);
		$values = explode(" ", $http_response_header[0]);
		switch($values[1]){
			case 400:
				echo "Bad request";
				return false;
			case 401:
				echo "Unauthorized";
				return false;
			case 404:
				echo "Not found";
				return false;
			case 429:
				echo "Rate limit exceeded";
				return false;
			case 500:
				echo "Internal server error";
				return false;
			case 503:
				echo "Service unavailable";
				return false;
		}
		
		// echo "Http request: " . $var . "<br>"; //dangerous!! shows api-key
		
		$result = json_decode($json, TRUE);
		return $result;
	}
	
	#all methods return an array
	#all uses summoner ID except for getSummoner
	
	#********************* champion-v1.2 ******************
	#omitted.
	
	#********************** game-v1.3 ********************
	#tons of data, very slow
	#structure for accessing return data: var[property]...
	function getRecentGames($var){
		$url = BASE . "/v1.3/game/by-summoner/" . $var . "/recent" . KEY;
		return getIt($url);
	}
	
	#********************* league-v2.4 ******************
	function getLeagueBySummoner($var){
		$url = BASE . "/v2.4/league/by-summoner/" . $var . KEY;
		$output = getIt($url);
		return ($output == "Not found" ? "Unranked" : $output);
	}
	
	function getLeagueByID($var){
		$url = BASE . "/v2.4/league/by-team/" . $var . KEY;
		return getIt($url);
	}
	
	function getChallengerLeague(){
		$url = BASE . "/v2.4/league/challenger" . KEY;
		return getIt($url);
	}
	
	#********************* lol-static-data-v1.2 *****************
	function getChampions(){
		$url = "https://na.api.pvp.net/api/lol/static-data/" . REGION . "/v1.2/champion" . KEY;
		return getIt($url);
	}
	
	function getChampionByID($var){
		$url = "https://na.api.pvp.net/api/lol/static-data/" . REGION . "/v1.2/champion/" . $var . KEY;
		return getIt($url);
	}
	
	function getItems(){
		$url = "https://na.api.pvp.net/api/lol/static-data/" . REGION . "/v1.2/item" . KEY;
		return getIt($url);
	}
	
	function getItemByID($var){
		$url = "https://na.api.pvp.net/api/lol/static-data/" . REGION . "/v1.2/item/" . $var . KEY;
		return getIt($url);
	}
	
	function getMasteries(){
		$url = "https://na.api.pvp.net/api/lol/static-data/" . REGION . "/v1.2/mastery" . KEY;
		return getIt($url);
	}
	
	function getMasteryByID($var){
		$url = "https://na.api.pvp.net/api/lol/static-data/" . REGION . "/v1.2/mastery/" . $var . KEY;
		return getIt($url);
	}
	
	function getRunes(){
		$url = "https://na.api.pvp.net/api/lol/static-data/" . REGION . "/v1.2/rune" . KEY;
		return getIt($url);
	}
	
	function getRuneByID($var){
		$url = "https://na.api.pvp.net/api/lol/static-data/" . REGION . "/v1.2/rune/" . $var . KEY;
		return getIt($url);
	}
	
	function getSpells(){
		$url = "https://na.api.pvp.net/api/lol/static-data/" . REGION . "/v1.2/summoner-spell" . KEY;
		return getIt($url);
	}
	
	function getSpellByID($var){
		$url = "https://na.api.pvp.net/api/lol/static-data/" . REGION . "/v1.2/summoner-spell/" . $var . KEY;
		return getIt($url);
	}
	
	function getRealm(){
		$ddragonUrl = "https://na.api.pvp.net/api/lol/static-data/" . REGION . "/v1.2/realm" . KEY;
		return getIt($ddragonUrl);
	}
	
	#********************* stats-v1.3 *******************
	function getRankedStats($var){
		$url = BASE . "/v1.3/stats/by-summoner/" . $var . "/ranked" . KEY;
		$output = getIt($url);
		return ($output == "Not found" ? "Unranked" : $output);
	}
	
	function getSummaryStats($var){
		$url = BASE . "/v1.3/stats/by-summoner/" . $var . "/summary" . KEY;
		return getIt($url);
	}
	
	#******************* summoner-v1.4 ***********************
	#structure for accessing return data: var[name/id][property]...
	#get summoner either by ID or name
	function getSummonerByName($var){
		$var = htmlentities($var);
		$url = BASE . "/v1.4/summoner/by-name/" . $var . KEY;
		return getIt($url);
	}
	
	function getSummonerByID($var){
		$url = BASE . "/v1.4/summoner/" . $var . KEY;
		return getIt($url);
	}
	
	function getSummonerMasteries($var){
		$url = BASE . "/v1.4/summoner/" . $var . "/masteries" . KEY;
		return getIt($url);
	}
	
	function getSummonerRunes($var){
		$url = BASE . "/v1.4/summoner/" . $var . "/runes" . KEY;
		return getIt($url);
	}
	
	#******************** team-v2.3 *******************
	function getTeamBySummoner($var){
		$url = BASE . "/v2.3/team/by-summoner/" . $var . KEY;
		$output = getIt($url);
		return ($output == "Not found" ? "No ranked teams" : $output);
	}
	
	function getTeamByID($var){
		$url = BASE . "/v2.3/team/" . $var . KEY;
		return getIt($url);
	}
	
	#code to name methods
	define('NAME', "leaguegrabber");
	define('PASSWORD', "YzsJ82yAyjuaQVXr");
	
	function code2name($code, $type){
		$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
		$result = mysqli_query($database, "SELECT * FROM $type WHERE id=$code LIMIT 1") or die(mysqli_error($database));
		$row = mysqli_fetch_array($result);
		return $row["name"];
	}
	
	function code2key($code, $type){
		$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
		$result = mysqli_query($database, "SELECT * FROM $type WHERE id=$code LIMIT 1") or die(mysqli_error($database));
		$row = mysqli_fetch_array($result);
		if($type == "champions"){
			return $row["champKey"];
		}else if($type == "spells"){
			return $row["spellKey"];
		}
		return $row["key"];
		
	}
	
	function cachePlayer($name, $data){
		$data = json_encode($data);
		$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
		mysqli_query($database, "INSERT INTO cache(name,data,lastUpdate) VALUES('$name', '$data','" . time() ."')") or die(mysqli_error($database));
	}
	
	function updateCache($name, $data){
		$data = json_encode($data);
		$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
		mysqli_query($database, "UPDATE cache SET data='$data',lastUpdate='" . time() . "' WHERE name='$name'") or die(mysqli_error($database));
	}
	
	//1 = no record, 2 = has record but need update, 3 = record is fresh
	function checkCache($name){
		$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
		$result = mysqli_query($database, "SELECT * FROM cache WHERE name = '$name' LIMIT 1") or die(mysqli_error($database));
		if(mysqli_num_rows($result) == 0){
			return 1;
		}else{
			$row = mysqli_fetch_array($result);
			return time() - $row['lastUpdate'] > WAIT_TIME ? 2 : 3;
		}
	}
	
	function getFromCache($name){
		$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
		$result = mysqli_query($database, "SELECT * FROM cache WHERE name = '$name' LIMIT 1") or die(mysqli_error($database));
		$sql = mysqli_fetch_array($result);
		$data = $sql['data'];
		return json_decode($data);
	}
	
	function checkSummonerFromDB($var){
		$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
		if(gettype($var) == "string"){
			$result = mysqli_query($database, "SELECT * FROM summoners WHERE name = '$var' LIMIT 1") or die(mysqli_error($database));
			if(mysqli_num_rows($result) == 0){
				return false;
			}else{
				$data = mysqli_fetch_array($result);
				return $data['id'];
			}
		}else{
			$result = mysqli_query($database, "SELECT * FROM summoners WHERE id = '$var' LIMIT 1") or die(mysqli_error($database));
			if(mysqli_num_rows($result) == 0){
				return false;
			}else{
				$data = mysqli_fetch_array($result);
				return $data['name'];
			}
		}
		
	}
	
	function addSummonertoDB($id,$name){
		$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
		$result = mysqli_query($database, "SELECT * FROM summoners WHERE id = '$id' LIMIT 1") or die(mysqli_error($database));
		if(mysqli_num_rows($result) == 0){
			mysqli_query($database, "INSERT INTO summoners(id,name) VALUES('$id','$name')") or die(mysqli_error($database));
		}
	}
	
	//returns an image URL, not json
	function getImage($var, $type){
		// $data = getRealm();
		if($type == "champion"){
			$data2 = code2key($var,"champions");
			return "<img src='/data/4.12.2/img/" . $type . "/" . $data2 . ".png' width = 50 height = 50>";
		}else if($type == "spell"){
			$data2 = code2key($var,"spells");
			return "<img src='/data/4.12.2/img/" . $type . "/" . $data2 . ".png' width = 50 height = 50>";
		}
		else{
			return "<img src='/data/4.12.2/img/" . $type . "/" . $var . ".png' width = 50 height = 50>";
		}
	}
	
	// echo code2name(12,"spells");
	// echo code2name(266,"champions");
	// echo getImage("Aatrox.png", "spell");
	#tests
	#$out = getSummoner($myID);
	#echo $out[$myID]["name"];
	#$out = getSummonerMasteries($myID);
	#$out = getSummonerRunes($myID);
	#$out = getSummaryStats($myID);
	#$out = getRecentGames($myID);
	#$out = getTeamBySummoner($myID);
	#$out = getLeagueBySummoner($myID);
	#$out = getSpells();
	#echo var_dump($out);
	#phpinfo();
	// echo "<pre>";
	// print_r(getChampions());
	// echo "</pre>";
?>
