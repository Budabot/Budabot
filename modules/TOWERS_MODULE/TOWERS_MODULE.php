<?php
	require_once 'Towers.class.php';

	$MODULE_NAME = "TOWERS_MODULE";

	//Tower messages
    bot::event("towers", "$MODULE_NAME/towers_messages.php", "none", "Show Attack Messages in chat"); 
	
	bot::command("", "$MODULE_NAME/towers_result.php", "battle", "all", "Shows the last Tower Attack messages");
	bot::command("", "$MODULE_NAME/towers_result.php", "battles", "all", "Shows the last Tower Attack messages");  // alias for !battle
  	bot::command("", "$MODULE_NAME/towers_result.php", "victory", "all", "Shows the last Tower Battle results");

	bot::regGroup("Tower_Battle", $MODULE_NAME, "Show Tower Attack Results", "battle", "victory", "battles");
	
	//Land Control Areas
  	bot::command("", "$MODULE_NAME/land_control_areas.php", "lca", "all", "Shows Infos about Land Control Areas");

	bot::addsetting("tower_attack_spam", "Layout types when displaying tower attacks", "edit", "1", "off;compact;normal;full", '0;1;2;3', "mod");
	bot::addsetting("tower_faction_def", "Display certain factions defending", "edit", "7", "none;clan;neutral;clan+neutral;omni;clan+omni;neutral+omni;all", '0;1;2;3;4;5;6;7', "mod");
	bot::addsetting("tower_faction_atk", "Display certain factions attacking", "edit", "7", "none;clan;neutral;clan+neutral;omni;clan+omni;neutral+omni;all", '0;1;2;3;4;5;6;7', "mod");

	//Setup
	bot::loadSQLFile($MODULE_NAME, "towerranges");
	
	//Helpfiles
	bot::help("towers", "$MODULE_NAME/towers.txt", "all", "Show Tower messages", "Towers");
	bot::help("lca", "$MODULE_NAME/lca.txt", "all", "Show Infos about Land Control Areas", "Towers");
?>
