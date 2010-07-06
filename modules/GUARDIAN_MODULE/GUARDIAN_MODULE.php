<?php
	$MODULE_NAME = "GUARDIAN_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Guardian module
	$this->event("leavePriv", "$MODULE_NAME/left_chat.php", "guard");
	$this->event("joinPriv", "$MODULE_NAME/joined_chat.php", "guard");
	$this->event("2sec", "$MODULE_NAME/guard_check.php", "guard");
	$this->command("", "$MODULE_NAME/guard_order.php", "guard", LEADER, "Show Guardian Order");
	$this->command("", "$MODULE_NAME/cast_guard.php", "g", ALL, "Show Guardian Cast");
	$this->regGroup("guardian", $MODULE_NAME, "Create a Guardian List", "guard", "g");
	$this->addsetting("guard_max", "Max Persons that are shown on Guard list", "edit", "10", "10;15;20;25;30", '0', MODERATOR, "$MODULE_NAME/guard_help.txt");

	//Help files
	$this->help("guard", "$MODULE_NAME/guard.txt", ALL, "Guardian Macro and List", "Guardian and Bior R. Commands");
?>