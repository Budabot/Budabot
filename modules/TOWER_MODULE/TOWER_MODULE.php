<?php
	require_once 'Towers.class.php';
	require_once 'functions.php';

	$db->loadSQLFile($MODULE_NAME, "tower_attack");
	$db->loadSQLFile($MODULE_NAME, "scout_info");
	$db->loadSQLFile($MODULE_NAME, "tower_site");

	$command->register($MODULE_NAME, "", "scout.php", "forcescout", "guild", "Adds tower info to watch list (bypasses some of the checks)");
	$command->register($MODULE_NAME, "", "scout.php", "scout", "guild", "Adds tower info to watch list");
	$command->register($MODULE_NAME, "", "remscout.php", "remscout", "guild", "Removes tower info to watch list", 'scout');
	$command->register($MODULE_NAME, "", "opentimes.php", "opentimes", "guild", "Shows status of towers", 'scout');

	$command->register($MODULE_NAME, "", "lc.php", "lc", "all", "Shows status of towers");

	$command->register($MODULE_NAME, "", "penalty.php", "penalty", "all", "Shows orgs in penalty");
	$command->register($MODULE_NAME, "", "victory.php", "victory", "all", "Shows the last Tower Battle results");
	$command->register($MODULE_NAME, "", "attacks.php", "attacks", "all", "Shows the last Tower Attack messages");
	CommandAlias::register($MODULE_NAME, "attacks", "battles");

	$setting->add($MODULE_NAME, "tower_attack_spam", "Layout types when displaying tower attacks", "edit", "options", "1", "off;compact;normal;full", '0;1;2;3');
	$setting->add($MODULE_NAME, "tower_faction_def", "Display certain factions defending", "edit", "options", "7", "none;clan;neutral;clan+neutral;omni;clan+omni;neutral+omni;all", '0;1;2;3;4;5;6;7');
	$setting->add($MODULE_NAME, "tower_faction_atk", "Display certain factions attacking", "edit", "options", "7", "none;clan;neutral;clan+neutral;omni;clan+omni;neutral+omni;all", '0;1;2;3;4;5;6;7');
	$setting->add($MODULE_NAME, "tower_page_size", "Number of results to display for victory/attacks", "edit", "options", "15", "5;10;15;20;25");

	$event->register($MODULE_NAME, "towers", "attack_messages.php", "Record attack messages");
	$event->register($MODULE_NAME, "towers", "victory_messages.php", "Record victory messages");

	// help files
	$help->register($MODULE_NAME, "attacks", "attacks.txt", "guild", "Show attack message commands and options");
	$help->register($MODULE_NAME, "victory", "victory.txt", "guild", "Show victory message commands and options");
	$help->register($MODULE_NAME, "scout", "scout.txt", "guild", "How to add a tower site to the watch list");
	$help->register($MODULE_NAME, "lc", "lc.txt", "all", "How to use land control commands");
	$help->register($MODULE_NAME, "penalty", "penalty.txt", "all", "How to show orgs who have attacked recently");
?>
