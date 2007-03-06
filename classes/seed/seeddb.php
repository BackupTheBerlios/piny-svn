<?php
	ini_set('include_path', ini_get('include_path').':/var/www/localhost/htdocs/piny/classes/');
	
	include_once('server/settings.php');
	require_once('db/db.php');
	
	define(PEERTYPE_PRINCIPAL, 'principal');
	define(PEERTYPE_VIRGIN,    'virgin');
	define(PEERTYPE_SENIOR,    'senior');
	define(PEERTYPE_JUNIOR,    'junior');
	
	function splitArray($array, $char) {
		$r = array();
		$num = 0;
		foreach ($array as $value) {
			$equal = strpos($value, $char);
			if ($equal === false) {
				$r[$num++] = $value;
			} else {
				$r[substr($value, 0, $equal)] = substr($value, $equal + strlen($char));
			}
		}
		return $r;
	}
	
	class peer {
        
        var $seed;
        
        function peer($seed) {
        	if (is_string($seed)) {
        		$this->seed = peer::getArrayFromSeed($seed);
			} else if (is_array($seed)) {
				$this->seed = $seed;
			}
        }
        
        function getHash()         { return $this->seed['Hash']; }
        function getIPType()       { return $this->seed['IPType']; }
        function getTags()         { return $this->seed['Tags']; }
        function getPort()         { return $this->seed['Port']; }
        function getIP()           { return $this->seed['IP']; }
        function getRI()           { return $this->seed['rI']; }
        function getUptime()       { return $this->seed['Uptime']; }
        function getVersion()      { return $this->seed['Version']; }
        function getUTC()          { return $this->seed['UTC']; }
        function getPeerType()     { return $this->seed['PeerType']; }
        function getSI()           { return $this->seed['sI']; }
        function getLastSeen()     { return $this->seed['LastSeen']; }
        function getName()         { return $this->seed['Name']; }
        function getCCount()       { return $this->seed['CCount']; }
        function getSCount()       { return $this->seed['SCount']; }
        function getNews()         { return $this->seed['news']; }
        function getUSpeed()       { return $this->seed['USpeed']; }
        function getCRTCount()     { return $this->seed['CRTCnt']; }
        function getCRWCount()     { return $this->seed['CRWCnt']; }
        function getBirthDate()    { return $this->seed['BDate']; }
        function getLinks()        { return $this->seed['LCount']; }
        function getRU()           { return $this->seed['rU']; }
        function getWords()        { return $this->seed['ICount']; }
        function getSU()           { return $this->seed['sU']; }
        function getISpeed()       { return $this->seed['ISpeed']; }
        function getRSpeed()       { return $this->seed['RSpeed']; }
        function getNCount()       { return $this->seed['NCount']; }
        function getFlags()        { return $this->seed['Flags']; }
        
        function setHash($value)         { $this->seed['Hash'] = $value; }
        function setIPType($value)       { $this->seed['IPType'] = $value; }
        function setTags($value)         { $this->seed['Tags'] = $value; }
        function setPort($value)         { $this->seed['Port'] = $value; }
        function setIP($value)           { $this->seed['IP'] = $value; }
        function setRI($value)           { $this->seed['rI'] = $value; }
        function setUptime($value)       { $this->seed['Uptime'] = $value; }
        function setVersion($value)      { $this->seed['Version'] = $value; }
        function setUTC($value)          { $this->seed['UTC'] = $value; }
        function setPeerType($value)     { $this->seed['PeerType'] = $value; }
        function setSI($value)           { $this->seed['sI'] = $value; }
        function setLastSeen($value)     { $this->seed['LastSeen'] = $value; }
        function setName($value)         { $this->seed['Name'] = $value; }
        function setCCount($value)       { $this->seed['CCount'] = $value; }
        function setSCount($value)       { $this->seed['SCount'] = $value; }
        function setNews($value)         { $this->seed['news'] = $value; }
        function setUSpeed($value)       { $this->seed['USpeed'] = $value; }
        function setCRTCount($value)     { $this->seed['CRTCnt'] = $value; }
        function setCRWCount($value)     { $this->seed['CRWCnt'] = $value; }
        function setBirthDate($value)    { $this->seed['BDate'] = $value; }
        function setLinks($value)        { $this->seed['LCount'] = $value; }
        function setRU($value)           { $this->seed['rU'] = $value; }
        function setWords($value)        { $this->seed['ICount'] = $value; }
        function setSU($value)           { $this->seed['sU'] = $value; }
        function setISpeed($value)       { $this->seed['ISpeed'] = $value; }
        function setRSpeed($value)       { $this->seed['RSpeed'] = $value; }
        function setNCount($value)       { $this->seed['NCount'] = $value; }
        function setFlags($value)        { $this->seed['Flags'] = $value; }
        
        function getVersionDbl() {
        	$version = $this->getVersion();
        	return (double)$version;
        }
        
        function getAddress() {
        	return $this->getIP() .':'. $this->getPort();
        }
        
        function toB64Seed() {
        	return 'b|'. base64_encode($this->toSeed());
        }
        
        function toPlainSeed() {
        	return 'p|'. $this->toSeed();
        }
        
        function toSeed($braces = true) {
        	if ($braces) $r = '{';
        	foreach ($this->seed as $key => $value) {
        		if ($key != 'PeerType') $r .= "$key=$value,";
        	}
        	$r = substr($r, 0, -1);
        	if ($braces) $sql .= '}';
        	return $r;
        }
        
        function getArrayFromSeed($seed) {
			switch (substr($seed, 0, 1)) {
				case 'p': $plainlist = substr($seed, 2); break;                   // plain text
				case 'b': $plainlist = base64_decode(substr($seed, 2)); break;    // base64-encoded
				default: $plainlist = substr($seed, 2); break;
			}
			$plainlist = substr($plainlist, 1, -1);        // kill '{' on beginning and '}' at the end
			$arr = splitArray(explode(',', $plainlist), '=');
			$r = array();
			foreach ($arr as $key => $val) {
				$r[trim($key)] = trim($val);
			}
			return $r;
		}
    }
    
    function seeddbGetPeer($seed) {
    	return new peer($seed);
    }
    
    function seeddbGetPeerFromHash($hash) {
    }
    
    function seeddbGetRandomSeeds($count, $includeMe = true) {
    	$seeddb = new db(SEEDDB);
    	$arr[] = $seeddb->getAssoc(
    			array(
    					'Hash',
    					'PeerType',
    					'IPType',
    					'Tags',
    					'Port',
    					'IP',
    					'rI',
    					'sI',
    					'rU',
    					'rI',
    					'Uptime',
    					'Version',
    					'LastSeen',
    					'Name',
    					'CCount',
    					'SCount',
    					'news',
    					'USpeed',
    					'CRTCnt',
    					'CRWCnt',
    					'BDate',
    					'LCount',
    					'ICount',
    					'ISpeed',
    					'RSpeed',
    					'Flags'
    			));
    	if (count($arr) < $count) $count = count($arr);
    	if ($count > 1) $r = array_rand($arr, $count - 1);
    	$mypeer = seeddbGetMyPeer();
    	$r[] = $mypeer->toB64Seed();
    	return $r;
    }
    
    function seeddbGetMyPeer() {
    	$seeddb = new db(SEEDDB);
    	$arr = $seeddb->getAssoc(
    			array(
    					'Hash',
    					'PeerType',
    					'IPType',
    					'Tags',
    					'Port',
    					'IP',
    					'rI',
    					'sI',
    					'rU',
    					'rI',
    					'Uptime',
    					'Version',
    					'LastSeen',
    					'Name',
    					'CCount',
    					'SCount',
    					'news',
    					'USpeed',
    					'CRTCnt',
    					'CRWCnt',
    					'BDate',
    					'LCount',
    					'ICount',
    					'ISpeed',
    					'RSpeed',
    					'Flags'
    			),
    			array('Hash' => MYHASH)
    	);
    	if ($arr === false || count($arr) == 0) die('MYHASH could not be found in seed-db');
    	return new peer($arr[0]);
    }
	
	function updatePeer($hash, $version, $uptime, $type) {
		$db = new db(SEEDDB);
		return $db->updateSingle(array('Version' => $version, 'Uptime' => $uptime, 'Type' => $type), array('Hash' => $hash));
	}
	
	function updateFromSeeds($seeds) {
		$db = new db(SEEDDB);
		$arr = array();
		foreach ($seeds as $seed) {
			$arr[] = peer::getArrayFromSeed($seed);
		}
		return $db->put($arr);
	}
?>