<?php
	
	include_once('../classes/server/settings.php');
	require_once('classes/seed/seeddb.php');
	require_once('classes/yacy/client.php');
	require_once('classes/yacy/version.php');
	require_once('classes/yacy/core.php');
	
	$post = settingsCheckInput($_GET);
	
	if ($post['key'] && $post['seed'] && $post['count']) {
		$peer = seeddbGetPeer($post['seed']);
		$key = $post['key'];
		$count = $post['count'];
		
		$myversion = settingsGetVersion();
		$uptime = settingsGetUptime();
		
		$urls = -1;
		$clientip = $_SERVER['REMOTE_ADDR'];
		$reportedip = $peer->getIP();
		
		// try the IP we have from the peer's seed
		if ($reportedip != $clientip && $peer->getVersionDbl() >= YACY_SUPPORTS_PORT_FORWARDING) {
			$yourip = $reportedip;
			$peer->setIP($reportedip);
			$urls = clientQueryURLCount($peer);
		}
		
		// if above failed, we use the IP, the peer connected from
		if ($urls < 0) {
			// TODO: check whether $clientip is our own address
			$yourip = $clientip;
			$peer->setIP($clientip);
			$urls = clientQueryURLCount($peer);
		}
		
		$peer->setLastSeenUTC();
		
		if ($urls >= 0) {
			if ($peer->getPeerType == PEERTYPE_PRINCIPAL) {
				$yourtype = PEERTYPE_PRINCIPAL;
			} else {
				$yourtype = PEERTYPE_SENIOR;
			}
			corePeerArrival($peer);
		} else {
			$yourtype = PEERTYPE_JUNIOR;
			$peer->setPeerType(PEERTYPE_JUNIOR);
			if ($peer->isProperSeed()) {
				corePeerPing($peer);
			}
		}
		
		$seeds = seeddbGetRandomSeeds($count, true);
		
		$result = "version=$myversion\n"
				."uptime=$uptime\n"
				."yourip=$yourip\n"
				."yourtype=$yourtype\n"
				."mytype=$mytype\n"
				.implode("\n", $seeds);
		
		echo $result;
		flush();
	} else {
		echo '-UNRESOLVED_PATTERN-';
	}
	
?>