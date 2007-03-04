<?php
	
	declare(MYHASH = 'pinytesthash')
	declare(SEEDDB = 'piny_seeddb');
	
	ini_set('include_path', ini_get('include_path').':/var/www/localhost/htdocs/');
	
	function settingsGetVersion() {
	}
	
	function settingsGetUptime() {
	}
	
	function settingsCheckInput($post) {
		return $post;
	}
	
?>