<?php
	$MODULE_NAME = "CAP_XP_MODULE";

	//Max XP calculator
	bot::command("", "$MODULE_NAME/cap_xp.php", "capsk", "all", "Max SK Calculator");
	bot::command("", "$MODULE_NAME/cap_xp.php", "capxp", "all", "Max XP Calculator");

	//Helpfiles
    bot::help("capxp", "$MODULE_NAME/max_experience.txt", "all", "Set your reasearch bar for max xp/sk", "Cap XP Module");
 
?>
