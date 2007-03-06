<?php
	
	ini_set('include_path', ini_get('include_path').':/var/www/localhost/htdocs/piny/classes/');
	
	include_once('server/settings.php');
	require_once('seed/seeddb.php');
	
	function clientQueryURLCount($peer) {
		$mypeer = seeddbGetMyPeer();
		$request = 'http://'. $peer->getAddress() .'/query.html'
				.'?iam='. $mypeer->getHash()
				.'&youare='. $peer->getHash()
				.'&key='
				.'&object=lurlcount'
				.'&env='
				.'&ttl=0';
		$result = splitArray(file_get_contents($request), '=');
		return $result['response'];
	}
	
?>