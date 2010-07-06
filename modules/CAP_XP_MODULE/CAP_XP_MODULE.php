<?php
$MODULE_NAME = "CAP_XP_MODULE";
$PLUGIN_VERSION = 0.1;

	//Max XP calculator
	$this->command("", "$MODULE_NAME/cap_xp.php", "capsk", ALL, "Max SK Calculator");
	$this->command("", "$MODULE_NAME/cap_xp.php", "capxp", ALL, "Max XP Calculator");

	//Help files
    $this->help("capxp", "$MODULE_NAME/max_experience.txt", ALL, "Set your reasearch bar for max xp/sk", "Cap XP Module");
 
?>
