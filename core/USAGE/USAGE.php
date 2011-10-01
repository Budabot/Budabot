<?php
	require_once 'Usage.class.php';

	$MODULE_NAME = "USAGE";
	
	DB::loadSQLFile($MODULE_NAME, "usage");
	
	Event::register($MODULE_NAME, "24hrs", "submit_usage.php", "none", "Submits usage stats to Budabot website");
    
	Command::register($MODULE_NAME, "", "usage_cmd.php", "usage", "guild", "Shows usage stats");
	
	Setting::add($MODULE_NAME, "record_usage_stats", "Enable recording usage stats", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, 'botid', 'Botid', 'noedit', 'text', '');
	
	Help::register($MODULE_NAME, "usage", "usage.txt", "guild", "How to show usage stats");
?>