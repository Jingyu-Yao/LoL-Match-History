<?php
#ini_set('display_startup_errors', 1);
#ini_set('display_errors', 1);
#error_reporting(-1);
require_once ('lolmethods.php');

#very basic stuff atm, need to expand if indepth stat analysis is to be performed

function updateChampions() {
	$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
	mysqli_query($database, "DELETE FROM `champions`") or die(mysqli_error($database));
	$data = getChampions();
	$data = $data["data"];
	foreach ($data as $entry) {
		$i = $entry['id'];
		$t = mysqli_real_escape_string($database, $entry['title']);
		$n = mysqli_real_escape_string($database, $entry['name']);
		$k = mysqli_real_escape_string($database, $entry['key']);
		mysqli_query($database, "INSERT INTO champions(id,title,name,champKey) VALUES('$i','$t','$n','$k')") or die(mysqli_error($database));
	}
	echo "Champions updated";
}

function updateItems() {
	$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
	mysqli_query($database, "DELETE FROM `items`") or die(mysqli_error($database));
	$data = getItems();
	$data = $data["data"];
	foreach ($data as $entry) {
		$i = $entry['id'];
		$n = mysqli_real_escape_string($database, $entry['description']);
		$k = mysqli_real_escape_string($database, $entry['name']);
		mysqli_query($database, "INSERT INTO items(id,description,name) VALUES('$i','$n','$k')") or die(mysqli_error($database));
	}
}

function updateMasteries() {
	$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
	mysqli_query($database, "DELETE FROM `masteries`") or die(mysqli_error($database));
	$data = getMasteries();
	$data = $data["data"];
	foreach ($data as $entry) {
		$i = $entry['id'];
		$k = mysqli_real_escape_string($database, $entry['name']);
		mysqli_query($database, "INSERT INTO masteries(id,name) VALUES('$i','$k')") or die(mysqli_error($database));
	}
	echo "Masteries updated";
}

function updateRunes() {
	$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
	mysqli_query($database, "DELETE FROM `runes`") or die(mysqli_error($database));
	$data = getRunes();
	$data = $data["data"];
	foreach ($data as $entry) {
		$i = $entry['id'];
		$k = mysqli_real_escape_string($database, $entry['name']);
		mysqli_query($database, "INSERT INTO runes(id,name) VALUES('$i','$k')") or die(mysqli_error($database));
	}
	echo "Runes updated";
}

function updateSpells() {
	$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
	mysqli_query($database, "DELETE FROM `spells`") or die(mysqli_error($database));
	$data = getSpells();
	$data = $data["data"];
	foreach ($data as $entry) {
		$i = $entry['id'];
		$n = mysqli_real_escape_string($database, $entry['name']);
		$j = mysqli_real_escape_string($database, $entry['key']);
		$k = mysqli_real_escape_string($database, $entry['summonerLevel']);
		mysqli_query($database, "INSERT INTO spells(id,name,spellKey,summonerLevel) VALUES('$i','$n','$j','$k')") or die(mysqli_error($database));
	}
	echo "Spells updated";
}

function updateRealm() {
	$database = mysqli_connect('localhost', NAME, PASSWORD, 'leaguedata');
	mysqli_query($database, "DELETE FROM `realm`") or die(mysqli_error($database));
	$data = getRealm();
	$encoded = json_encode($data);
	mysqli_query($database, "INSERT INTO realm(data) VALUES('$encoded')") or die(mysqli_error($database));
	echo "Realm updated";
}

function updateAll() {
	updateChampions();
	updateItems();
	updateMasteries();
	updateRunes();
	updateSpells();
	updateRealm();
}
// updateRealm();
updateAll();
?>
