-- openhellos: holding the hashes to the peers and the key used to communicate

CREATE TABLE `openhellos` (
	`Hash` char(12),
	`key` smallint UNSIGNED,
	PRIMARY KEY (`Hash`)
);