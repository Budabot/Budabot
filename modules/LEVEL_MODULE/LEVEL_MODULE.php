<?php
	require_once 'Level.class.php';

	$MODULE_NAME = "LEVEL_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, 'levels');

    // Level Info
	Command::register($MODULE_NAME, "", "level.php", "level", "all", "Show level ranges");
	CommandAlias::register($MODULE_NAME, "level", "pvp");
	CommandAlias::register($MODULE_NAME, "level", "lvl");

	// Missions
	Command::register($MODULE_NAME, "", "missions.php", "missions", "all", "Shows what missions a specified level can roll");
	CommandAlias::register($MODULE_NAME, "missions", "mission");
	
	// XP/SK Calculator
	Command::register($MODULE_NAME, "", "xp.php", "xp", "all", "XP Calculator");
	CommandAlias::register($MODULE_NAME, "xp", "sk");
	
	// AXP calculator
	Command::register($MODULE_NAME, "", "axp.php", "axp", "all", "AXP Calculator");

	// Help files
    Help::register($MODULE_NAME, "level", "level.txt", "all", "How to use level");
    Help::register($MODULE_NAME, "missions", "missions.txt", "all", "Who can roll a specific QL of a mission");
	Help::register($MODULE_NAME, "xp", "xp.txt", "all", "XP/SK/AXP Info");
?>
