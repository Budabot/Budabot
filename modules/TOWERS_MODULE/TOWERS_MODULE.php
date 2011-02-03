<?php
	require_once 'Towers.class.php';
	require_once 'functions.php';

	$MODULE_NAME = "TOWERS_MODULE";

	bot::loadSQLFile($MODULE_NAME, "tower_attack");
	bot::loadSQLFile($MODULE_NAME, "scout_info");
	bot::loadSQLFile($MODULE_NAME, "tower_site");

	bot::command("", "$MODULE_NAME/scout.php", "forcescout", "guild", "Adds tower info to watch list (bypasses some of the checks)");
	bot::command("", "$MODULE_NAME/scout.php", "scout", "guild", "Adds tower info to watch list");
	bot::command("", "$MODULE_NAME/remscout.php", "remscout", "guild", "Removes tower info to watch list");

	bot::command("", "$MODULE_NAME/opentimes.php", "opentimes", "guild", "Shows status of towers");
	bot::command("", "$MODULE_NAME/lc.php", "lc", "all", "Shows status of towers");

	bot::command("", "$MODULE_NAME/attacks.php", "attacks", "all", "Shows the last Tower Attack messages");
	bot::command("", "$MODULE_NAME/attacks.php", "battle", "all", "Shows the last Tower Attack messages");
	bot::command("", "$MODULE_NAME/attacks.php", "battles", "all", "Shows the last Tower Attack messages");

  	bot::command("", "$MODULE_NAME/victory.php", "victory", "all", "Shows the last Tower Battle results");

	bot::addsetting($MODULE_NAME, "tower_attack_spam", "Layout types when displaying tower attacks", "edit", "1", "off;compact;normal;full", '0;1;2;3', "mod");
	bot::addsetting($MODULE_NAME, "tower_faction_def", "Display certain factions defending", "edit", "7", "none;clan;neutral;clan+neutral;omni;clan+omni;neutral+omni;all", '0;1;2;3;4;5;6;7', "mod");
	bot::addsetting($MODULE_NAME, "tower_faction_atk", "Display certain factions attacking", "edit", "7", "none;clan;neutral;clan+neutral;omni;clan+omni;neutral+omni;all", '0;1;2;3;4;5;6;7', "mod");

	bot::event($MODULE_NAME, "towers", "attack_messages.php", "none", "Record attack messages");
	bot::event($MODULE_NAME, "towers", "victory_messages.php", "none", "Record victory messages");

	// help files
	bot::help($MODULE_NAME, "attacks", "attacks.txt", "guild", "Show attack message commands and options");
	bot::help($MODULE_NAME, "victory", "victory.txt", "guild", "Show victory message commands and options");
	bot::help($MODULE_NAME, "scout", "scout.txt", "guild", "How to add a tower site to the watch list");
	bot::help($MODULE_NAME, "lc", "lc.txt", "all", "How to use land control commands");
?>
