<?
	
	require_once('classes/server/settings.php');
	require_once('classes/db/db.php');
	
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
				switch (substr($seed, 0, 1)) {
					case 'p': $plainlist = substr($seed, 2); break;                   // plain text
					case 'b': $plainlist = base64_decode(substr($seed, 2)); break;    // base64-encoded
					default: $plainlist = substr($seed, 2); break;
				}
				$plainlist = substr($plainlist, 1, -1);        // kill '{' on beginning and '}' at the end
				$this->seed = splitArray(explode(',', $plainlist), '=');
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
    }
    
    function seeddbGetPeer($seed) {
    	return new peer($seed);
    }
    
    function seeddbGetMyPeer() {
    	return new peer(dbGetFromHash(MYHASH, SEEDDB));
    }
	
?>