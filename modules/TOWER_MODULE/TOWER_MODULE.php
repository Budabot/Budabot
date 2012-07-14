<?php
	require_once 'Towers.class.php';
	require_once 'functions.php';

	$chatBot->registerInstance($MODULE_NAME, 'Towers', new Towers);

	$db->loadSQLFile($MODULE_NAME, "tower_attack");
	$db->loadSQLFile($MODULE_NAME, "scout_info");
	$db->loadSQLFile($MODULE_NAME, "tower_site");

	$command->register($MODULE_NAME, "", "scout.php", "forcescout", "guild", "Adds tower info to watch list (bypasses some of the checks)", "scout.txt");
	$command->register($MODULE_NAME, "", "scout.php", "scout", "guild", "Adds tower info to watch list", "scout.txt");
	$command->register($MODULE_NAME, "", "remscout.php", "remscout", "guild", "Removes tower info to watch list", "scout.txt");
	$command->register($MODULE_NAME, "", "opentimes.php", "opentimes", "guild", "Shows status of towers", "scout.txt");

	$command->register($MODULE_NAME, "", "lc.php", "lc", "all", "Shows status of towers", "lc.txt");

	$command->register($MODULE_NAME, "", "penalty.php", "penalty", "all", "Shows orgs in penalty", "penalty.txt");
	$command->register($MODULE_NAME, "", "victory.php", "victory", "all", "Shows the last Tower Battle results", "victory.txt");
	$command->register($MODULE_NAME, "", "attacks.php", "attacks", "all", "Shows the last Tower Attack messages", "attacks.txt");
	$commandAlias->register($MODULE_NAME, "attacks", "battles");

	$setting->add($MODULE_NAME, "tower_attack_spam", "Layout types when displaying tower attacks", "edit", "options", "1", "off;compact;normal;full", '0;1;2;3');
	$setting->add($MODULE_NAME, "tower_faction_def", "Display certain factions defending", "edit", "options", "7", "none;clan;neutral;clan+neutral;omni;clan+omni;neutral+omni;all", '0;1;2;3;4;5;6;7');
	$setting->add($MODULE_NAME, "tower_faction_atk", "Display certain factions attacking", "edit", "options", "7", "none;clan;neutral;clan+neutral;omni;clan+omni;neutral+omni;all", '0;1;2;3;4;5;6;7');
	$setting->add($MODULE_NAME, "tower_page_size", "Number of results to display for victory/attacks", "edit", "options", "15", "5;10;15;20;25");

	$event->register($MODULE_NAME, "towers", "attack_messages.php", "Record attack messages");
	$event->register($MODULE_NAME, "towers", "victory_messages.php", "Record victory messages");
?>
