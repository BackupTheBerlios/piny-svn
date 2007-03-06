<?
	
	include_once('../server/settings.php');
	require_once('db/db.php');
	
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
        		$this->seed = self::getArrayFromSeed($seed);
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
        
        function getAddress() {
        	return $this->getIP() .':'. $this->getPort();
        }
        
        function toB64Seed() {
        	return base64_encode($this->toSeed());
        }
        
        function toSeed() {
        	return '{'. implode(',', $this->seed) .'}';
        }
        
        function getArrayFromSeed($seed) {
			switch (substr($seed, 0, 1)) {
				case 'p': $plainlist = substr($seed, 2); break;                   // plain text
				case 'b': $plainlist = base64_decode(substr($seed, 2)); break;    // base64-encoded
				default: $plainlist = substr($seed, 2); break;
			}
			$plainlist = substr($plainlist, 1, -1);        // kill '{' on beginning and '}' at the end
			return splitArray(explode(',', $plainlist), '=');
		}
    }
    
    function seeddbGetPeer($seed) {
    	return new peer($seed);
    }
    
    function seeddbGetMyPeer() {
    	$seeddb = new db(SEEDDB);
    	$arr = $seeddb->getAssoc(
    			array(
    					'Hash',
    					'Type' => 'PeerType',
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
    	if ($arr === false) throw new Exception('MYHASH could not be found in seed-db');
    	return new seed($arr[0]);
    }
	
	function updatePeer($hash, $version, $uptime, $type) {
		$db = new db(SEEDDB);
		return $db->updateSingle(array('Version' => $version, 'Uptime' => $uptime, 'Type' => $type), array('Hash' => $hash));
	}
	
	function updateFromSeeds($seeds) {
		$db = new db(SEEDDB);
		$i = 0;
		foreach ($seeds as $seed) {
			if ($db->putSingle(peer::getArrayFromSeed($seed))) $i++;
		}
		return $i;
	}
?>