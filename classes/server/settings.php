<?php
	
	define(MYHASH, 'pinytesthash');
	define(SEEDDB, 'piny_seeddb');
	
	define(DB_MYSQL, 'mysql');
	define(DB_PGSQL, 'pg');
	define(DB_MYQSLI, 'mysqli');
	
	define(DB_TYPE, DB_MYSQL);
	define(DB_HOST, 'localhost');
	define(DB_PORT, '3306');
	define(DB_USER, 'root');
	define(DB_PWD, '');
	define(DB_NAME, 'piny');
	
	ini_set('include_path', ini_get('include_path').':/var/www/localhost/htdocs/piny/classes/');
	
	function settingsGetVersion() {
		return 0.5;
	}
	
	function settingsGetUptime() {
		return 0;
	}
	
	function settingsCheckInput($post) {
		foreach ($post as $key => $value) {
			$post[$key] = strip_tags($value);
		}
		return $post;
	}
	
?>