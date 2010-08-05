<?php
	$MODULE_NAME = "BIOR_MODULE";

	//Bio Regrowth module
	bot::event("leavePriv", "$MODULE_NAME/left_chat.php", "bior", "Remove player who leaves chat from bior list if he was on it");
	bot::event("joinPriv", "$MODULE_NAME/joined_chat.php", "bior", "Add player to bior list when he joins chat if he should be on it (Keep,Adv,Enf,Eng)");
	bot::event("2sec", "$MODULE_NAME/bior_check.php", "bior", "Timer check for bior list");
	
	bot::command("", "$MODULE_NAME/bior_order.php", "bior", "leader", "Show Bio Regrowth Order");
	bot::command("", "$MODULE_NAME/cast_bior.php", "b", "all", "Show Bio Regrowth Cast");
	
	bot::regGroup("bior", $MODULE_NAME, "Create a Bio Regrowth List", "bior", "b");
	
	bot::addsetting("bior_max", "Max Persons that are shown on BioR list", "edit", "10", "10;15;20;25;30", '0', "mod", "$MODULE_NAME/bior_help.txt");

	//Helpfiles
	bot::help("bior", "$MODULE_NAME/bior.txt", "all", "Bio Regrowth Macro and List", "Guardian and Bior R. Commands");
?>