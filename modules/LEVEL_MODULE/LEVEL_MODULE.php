<?php
	$MODULE_NAME = "LEVEL_MODULE";
	$PLUGIN_VERSION = 0.1;
	$FOLDER = $dir;
	
	bot::loadSQLFile($MODULE_NAME, 'levels');

    //Level Infos
	bot::command("", "$MODULE_NAME/level.php", "pvp", ALL, "Show level ranges");
	bot::command("", "$MODULE_NAME/level.php", "level", ALL, "Show level ranges");
	bot::command("", "$MODULE_NAME/level.php", "lvl", ALL, "Show level ranges");
	bot::regGroup("lvlrng", $MODULE_NAME, "Show level ranges", "lvl", "level");

	//Missions
	bot::command("", "$MODULE_NAME/missions.php", "mission", ALL);
	bot::command("", "$MODULE_NAME/missions.php", "missions", ALL);
	
	//XP/SK/AXP Calculator
	bot::command("", "$MODULE_NAME/xp_sk_calc.php", "sk", ALL, "SK Calculator");
	
	bot::command("", "$MODULE_NAME/xp_sk_calc.php", "xp", ALL, "XP Calculator");

	bot::command("", "$MODULE_NAME/axp.php", "axp", ALL, "AXP Calculator");
	bot::regGroup("EXP", $MODULE_NAME, "Calculate needed XP/SK/AXP", "sk", "xp", "axp");

	//Title Levels
	bot::command("", "$MODULE_NAME/title.php", "title", ALL, "Show the Titlelevels and how much IP/Level");

	//Help files
    bot::help("level", "$MODULE_NAME/level.txt", ALL, "Levelinfos");
    bot::help("title_level", "$MODULE_NAME/title.txt", ALL, "Infos about TitleLevels");
    bot::help("missions", "$MODULE_NAME/missions.txt", ALL, "Who can roll a specific QL of a mission");
	bot::help("experience", "$MODULE_NAME/experience.txt", ALL, "XP/SK/AXP Infos");
?>
