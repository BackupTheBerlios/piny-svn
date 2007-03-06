-- `seeddb` table structure
--
-- FlexWidthArray properties: rowdef=byte[] key-20, byte[] node-480

CREATE TABLE `piny_seeddb` (
	`Hash` char(12),			-- peer-hash, 12 byte
	`Type` tinyint,				-- peer-type: 0 = virgin, 1 = junior, 2 = senior, 3 = principal
	`IPType` varchar(40),		-- always "&empty;" as far as I have seen
	`Tags` varchar(255),		-- self-defined tags for the own index
	`Port` smallint UNSIGNED,	-- peer-port (0-65535)
	`IP` varchar(40),			-- peer-ip or (if set) static hostname
	`rI` integer UNSIGNED,		-- number of received words
	`sI` integer UNSIGNED,		-- number of sent words
	`rU` integer UNSIGNED,		-- number of received URLs
	`sU` integer UNSIGNED,		-- number of sent URLs
	`Uptime` timestamp,			-- the peer's uptime
	`Version` double(14,12),	-- the peer's yacy-version, i.e. v13.5230123443
	`LastSeen` timestamp,		-- time this peer was last pinged
	`Name` varchar(40),			-- the peer's name
	`CCount` float(5),			-- connects per hour, i.e. 0.48
	`SCount` smallint UNSIGNED,	-- number of seeds, this peer has stored
	`news` varchar(255)			-- any news this peer knows about
	`USpeed` smallint UNSIGNED,	-- computed uplink speed of this peer
	`CRTCnt` smallint UNSIGNED,	-- number of files: citation rank other
	`CRWCnt` smallint UNSIGNED,	-- number of files: citation rank own
	`BDate` timestamp,			-- timestamp of first start-up
	`LCount` integer UNSIGNED,	-- number of links this peer has stored (LURL)
	`ICount` integer UNSIGNED,	-- number of words this peer has stored (RWI)
	`ISpeed` float(5),			-- indexed pages per minute, PPM
	`RSpeed` float(5),			-- queries per minute
	`Flags` char(4),			-- peer-flags
	PRIMARY KEY (`Hash`)
);
