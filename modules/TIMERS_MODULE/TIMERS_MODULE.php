<?php
	require_once 'Timer.class.php';
	require_once 'Stopwatch.class.php';

	$db->loadSQLFile($MODULE_NAME, 'timers');
	
	$chatBot->registerInstance($MODULE_NAME, 'timer', new Timer);
	$chatBot->registerInstance($MODULE_NAME, 'stopwatch', new Stopwatch);
	
	$command->register($MODULE_NAME, "", "countdown.php", "countdown", "guild", "Set a countdown", "countdown.txt");
	$commandAlias->register($MODULE_NAME, "countdown", "cd");
?>