<?php
	
	ini_set('include_path', ini_get('include_path').':/var/www/localhost/htdocs/piny/classes/');
	
	include_once('server/settings.php');
	include_once('yacy/errors.php');
	require_once('db/db.php');
	require_once('seed/seeddb.php');
	
	function corePeerPing($peer) {
		$key = rand(0,10000);
		$db = new db(OPENHELLOS);
		if (!$db->addSingle(array('key' => $key, 'Hash' => $peer->getHash()))) die(ERR_YACY_DB_SAVE);
		$host = 'http://'. $peer->getAddress() .'/yacy/hello.html';
		$args = "key=$key&seed=".MYHASH."&count=20";
		$hello = explode("\n", file_get_contents("$host?$args"));
		$arr = splitArray(array_slice($hello, 0, 4));
		updatePeer($peer->getHash(), $arr['myversion'], $arr['uptime'], $arr['mytype']);
		updateFromSeeds(array_slice($hello, 5));
	}
	
	function corePeerArrival($peer) {
	}
	
?>