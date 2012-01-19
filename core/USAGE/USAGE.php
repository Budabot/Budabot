<?php
	require_once 'Usage.class.php';
	
	$chatBot->registerInstance($MODULE_NAME, 'Usage', new Usage);
	
	$db->loadSQLFile($MODULE_NAME, "usage");
	
	$event->register($MODULE_NAME, "24hrs", "Usage.submitUsage", "Submits anonymous usage stats to Budabot website", "usage.txt");
    
	$command->register($MODULE_NAME, "", "usage_cmd.php", "usage", "guild", "Shows usage stats", '', 1);
	
	$setting->add($MODULE_NAME, "record_usage_stats", "Enable recording usage stats", "edit", "options", "1", "true;false", "1;0");
	$setting->add($MODULE_NAME, 'botid', 'Botid', 'noedit', 'text', '');
	$setting->add($MODULE_NAME, 'last_submitted_stats', 'last_submitted_stats', 'noedit', 'text', 0);
?>