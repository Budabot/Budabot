<?php
	require_once 'Preferences.class.php';
	
	$chatBot->registerInstance($MODULE_NAME, 'Preferences', new Preferences);
	
	$db->loadSQLFile($MODULE_NAME, 'preferences');
?>