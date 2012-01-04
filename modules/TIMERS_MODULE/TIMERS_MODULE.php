<?php
	require_once 'Timer.class.php';
	require_once 'Stopwatch.class.php';

	$db->loadSQLFile($MODULE_NAME, 'timers');
	
	$chatBot->registerInstance($MODULE_NAME, 'timer', new Timer);
	$chatBot->registerInstance($MODULE_NAME, 'stopwatch', new Stopwatch);
	
	$command->register($MODULE_NAME, "", "countdown.php", "countdown", "guild", "Set a countdown");
	$commandAlias->register($MODULE_NAME, "countdown", "cd");

	$help->register($MODULE_NAME, "timers", "timers.txt", "guild", "How to create and show timers");
	$help->register($MODULE_NAME, "stopwatch", "stopwatch.txt", "guild", "How to use the stopwatch");
	$help->register($MODULE_NAME, "countdown", "countdown.txt", "guild", "How to create a 5 second countdown timer");
?>