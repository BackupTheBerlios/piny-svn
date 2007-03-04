<?php
	
	require_once('classes/seed/seeddb.php');
	
	function clientQueryURLCount($peer) {
		$request = 'http://'. $peer->getAddress() .'/query.html'
				.'?iam='. seeddbGetMyPeer()->getHash()
				.'&youare='. $peer->getHash()
				.'&key='
				.'&object=lurlcount'
				.'&env='
				.'&ttl=0';
		$result = splitArray(file_get_contents($request), '=');
		return $result['response'];
	}
	
?>