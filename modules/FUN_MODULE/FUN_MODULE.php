<?php
	require_once 'Fun.class.php';

	$chatBot->registerInstance($MODULE_NAME, 'Fun', new Fun);

	$command->register($MODULE_NAME, "", "fight.php", "fight", "all", "Let two persons fight against each other", 'fun_module.txt');
	$command->register($MODULE_NAME, "", "ding.php", "ding", "all", "Shows a random ding gratz message", 'fun_module.txt');
?>
