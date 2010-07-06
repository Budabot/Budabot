<?php
	$MODULE_NAME = "BIOR_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Bio Regrowth module
	$this->event("leavePriv", "$MODULE_NAME/left_chat.php", "bior");
	$this->event("joinPriv", "$MODULE_NAME/joined_chat.php", "bior");
	$this->event("2sec", "$MODULE_NAME/bior_check.php", "bior");
	
	$this->command("", "$MODULE_NAME/bior_order.php", "bior", LEADER, "Show Bio Regrowth Order");
	$this->command("", "$MODULE_NAME/cast_bior.php", "b", ALL, "Show Bio Regrowth Cast");
	
	$this->regGroup("bior", $MODULE_NAME, "Create a Bio Regrowth List", "bior", "b");
	
	$this->addsetting("bior_max", "Max Persons that are shown on BioR list", "edit", "10", "10;15;20;25;30", '0', MODERATOR, "$MODULE_NAME/bior_help.txt");

	//Help files
	$this->help("bior", "$MODULE_NAME/bior.txt", ALL, "Bio Regrowth Macro and List");
?>