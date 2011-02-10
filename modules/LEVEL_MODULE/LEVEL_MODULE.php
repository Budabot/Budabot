<?php
	require_once 'Level.class.php';

	$MODULE_NAME = "LEVEL_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, 'levels');

    //Level Infos
	Command::register($MODULE_NAME, "", "level.php", "pvp", "all", "Show level ranges");
	Command::register($MODULE_NAME, "", "level.php", "level", "all", "Show level ranges");
	Command::register($MODULE_NAME, "", "level.php", "lvl", "all", "Show level ranges");

	//Missions
	Command::register($MODULE_NAME, "", "missions.php", "mission", "all");
	Command::register($MODULE_NAME, "", "missions.php", "missions", "all");
	
	//XP/SK/AXP Calculator
	Command::register($MODULE_NAME, "", "xp_sk_calc.php", "sk", "all", "SK Calculator");
	Command::register($MODULE_NAME, "", "xp_sk_calc.php", "xp", "all", "XP Calculator");
	Command::register($MODULE_NAME, "", "axp.php", "axp", "all", "AXP Calculator");

	//Title Levels
	Command::register($MODULE_NAME, "", "title.php", "title", "all", "Show the Titlelevels and how much IP/Level");

	//Help files
    Help::register($MODULE_NAME, "level", "level.txt", "all", "How to use level");
    Help::register($MODULE_NAME, "title", "title.txt", "all", "How to use title");
    Help::register($MODULE_NAME, "missions", "missions.txt", "all", "Who can roll a specific QL of a mission");
	Help::register($MODULE_NAME, "xp", "xp.txt", "all", "XP/SK/AXP Info");
?>
