<?php
$MODULE_NAME = "CAP_XP_MODULE";
$PLUGIN_VERSION = 0.1;

	//Max XP calculator
	bot::command("", "$MODULE_NAME/cap_xp.php", "capsk", ALL, "Max SK Calculator");
	bot::command("", "$MODULE_NAME/cap_xp.php", "capxp", ALL, "Max XP Calculator");

	//Help files
    bot::help("capxp", "$MODULE_NAME/max_experience.txt", ALL, "Set your reasearch bar for max xp/sk", "Cap XP Module");
 
?>
