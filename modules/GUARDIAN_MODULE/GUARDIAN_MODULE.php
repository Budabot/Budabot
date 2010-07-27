<?php
	$MODULE_NAME = "GUARDIAN_MODULE";

	//Guardian module
	bot::event("leavePriv", "$MODULE_NAME/left_chat.php", "guard");
	bot::event("joinPriv", "$MODULE_NAME/joined_chat.php", "guard");
	bot::event("2sec", "$MODULE_NAME/guard_check.php", "guard");
	bot::command("", "$MODULE_NAME/guard_order.php", "guard", "leader", "Show Guardian Order");
	bot::command("", "$MODULE_NAME/cast_guard.php", "g", "all", "Show Guardian Cast");
	bot::regGroup("guardian", $MODULE_NAME, "Create a Guardian List", "guard", "g");
	bot::addsetting("guard_max", "Max Persons that are shown on Guard list", "edit", "10", "10;15;20;25;30", '0', "mod", "$MODULE_NAME/guard_help.txt");

	//Helpfiles
	bot::help("guard", "$MODULE_NAME/guard.txt", "all", "Guardian Macro and List", "Guardian and Bior R. Commands");
?>