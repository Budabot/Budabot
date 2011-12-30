<?php
	require_once 'Level.class.php';

	$db->loadSQLFile($MODULE_NAME, 'levels');

    // Level Info
	$command->register($MODULE_NAME, "", "level.php", "level", "all", "Show level ranges");
	CommandAlias::register($MODULE_NAME, "level", "pvp");
	CommandAlias::register($MODULE_NAME, "level", "lvl");

	// Missions
	$command->register($MODULE_NAME, "", "missions.php", "missions", "all", "Shows what missions a specified level can roll");

	// XP/SK Calculator
	$command->register($MODULE_NAME, "", "xp.php", "xp", "all", "XP Calculator");
	CommandAlias::register($MODULE_NAME, "xp", "sk");

	// AXP calculator
	$command->register($MODULE_NAME, "", "axp.php", "axp", "all", "AXP Calculator", 'xp');

	// Max XP calculator
	$command->register($MODULE_NAME, "", "capxp.php", "capxp", "all", "Max XP Calculator");
	CommandAlias::register($MODULE_NAME, "capxp", "capsk");

	// Help files
    $help->register($MODULE_NAME, "level", "level.txt", "all", "How to use level");
    $help->register($MODULE_NAME, "missions", "missions.txt", "all", "Who can roll a specific QL of a mission");
	$help->register($MODULE_NAME, "xp", "xp.txt", "all", "XP/SK/AXP Info");
	$help->register($MODULE_NAME, "capxp", "capxp.txt", "all", "Set your reasearch bar for max xp/sk");
?>
