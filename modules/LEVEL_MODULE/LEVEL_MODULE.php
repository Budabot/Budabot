<?php
	require_once 'Level.class.php';

	$db->loadSQLFile($MODULE_NAME, 'levels');

    // Level Info
	$command->register($MODULE_NAME, "", "level.php", "level", "all", "Show level ranges", "level.txt");
	$commandAlias->register($MODULE_NAME, "level", "pvp");
	$commandAlias->register($MODULE_NAME, "level", "lvl");

	// Missions
	$command->register($MODULE_NAME, "", "missions.php", "missions", "all", "Shows what missions a specified level can roll", "missions.txt");

	// XP/SK Calculator
	$command->register($MODULE_NAME, "", "xp.php", "xp", "all", "XP Calculator", "xp.txt");
	$commandAlias->register($MODULE_NAME, "xp", "sk");

	// AXP calculator
	$command->register($MODULE_NAME, "", "axp.php", "axp", "all", "AXP Calculator", 'xp.txt');

	// Max XP calculator
	$command->register($MODULE_NAME, "", "capxp.php", "capxp", "all", "Max XP Calculator", "capxp.txt");
	$commandAlias->register($MODULE_NAME, "capxp", "capsk");
?>
