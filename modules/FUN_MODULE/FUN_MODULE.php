<?php
	require_once 'Fun.class.php';
	
	$chatBot->registerInstance($MODULE_NAME, 'Fun', new Fun);

	$command->register($MODULE_NAME, "", "fight.php", "fight", "all", "Let two persons fight against each other", 'fun_module.txt');
	$command->register($MODULE_NAME, "", "ding.php", "ding", "all", "Shows a random ding gratz message", 'fun_module.txt');
	$command->register($MODULE_NAME, "", "Fun.fcCommand", "fc", "all", "Shows a random FC quote", 'fun_module.txt');
	$command->register($MODULE_NAME, "", "doh.php", "doh", "all", "Shows a random doh message", 'fun_module.txt');
	$command->register($MODULE_NAME, "", "beer.php", "beer", "all", "Shows a random beer message", 'fun_module.txt');
	$command->register($MODULE_NAME, "", "cybor.php", "cybor", "all", "Shows a random cybor message", 'fun_module.txt');
	$command->register($MODULE_NAME, "", "chuck.php", "chuck", "all", "Shows a random Chuck Norris joke", 'fun_module.txt');
	$command->register($MODULE_NAME, "", "credz.php", "credz", "all", "Shows a random credits message", 'fun_module.txt');
	$command->register($MODULE_NAME, "", "homer.php", "homer", "all", "Shows a random homer quote message", 'fun_module.txt');
	$command->register($MODULE_NAME, "", "dwight.php", "dwight", "all", "Shows a random dwight quote message", 'fun_module.txt');
	$command->register($MODULE_NAME, "", "brain.php", "brain", "all", "Shows a random pinky and the brain quote message", 'fun_module.txt');
    $command->register($MODULE_NAME, "", "pirates.php", "Pirates", "all", "Shows a random Pirates of the Caribbean quote message", 'fun_module.txt');
	$commandAlias->register($MODULE_NAME, "brain", "pinky");
?>