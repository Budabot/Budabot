<?php
	$MODULE_NAME = "LEVEL_MODULE";
	$PLUGIN_VERSION = 0.1;
	$FOLDER = $dir;
	
	bot::loadSQLFile($MODULE_NAME, 'levels');

    //Level Infos
	bot::command("", "$MODULE_NAME/level.php", "pvp", "all", "Show level ranges");
	bot::command("", "$MODULE_NAME/level.php", "level", "all", "Show level ranges");
	bot::command("", "$MODULE_NAME/level.php", "lvl", "all", "Show level ranges");
	bot::regGroup("lvlrng", $MODULE_NAME, "Show level ranges", "lvl", "level");

	//Missions
	bot::command("", "$MODULE_NAME/missions.php", "mission", "all");
	bot::command("", "$MODULE_NAME/missions.php", "missions", "all");
	
	//XP/SK/AXP Calculator
	bot::command("", "$MODULE_NAME/xp_sk_calc.php", "sk", "all", "SK Calculator");
	
	bot::command("", "$MODULE_NAME/xp_sk_calc.php", "xp", "all", "XP Calculator");

	bot::command("", "$MODULE_NAME/axp.php", "axp", "all", "AXP Calculator");
	bot::regGroup("EXP", $MODULE_NAME, "Calculate needed XP/SK/AXP", "sk", "xp", "axp");

	//Title Levels
	bot::command("", "$MODULE_NAME/title.php", "title", "guild", "Show the Titlelevels and how much IP/Level");

	//Helpfiles
	bot::help("pvpranges", "$MODULE_NAME/pvpranges.txt", "guild", "Pvp ranges", "Helpbot");
    bot::help("level", "$MODULE_NAME/level.txt", "guild", "Levelinfos", "Helpbot");
    bot::help("title_level", "$MODULE_NAME/title.txt", "guild", "Infos about TitleLevels", "Helpbot");
    bot::help("missions", "$MODULE_NAME/missions.txt", "guild", "Who can roll a specific QL of a mission", "Helpbot");
	bot::help("experience", "$MODULE_NAME/experience.txt", "guild", "XP/SK/AXP Infos", "Helpbot");
?>
