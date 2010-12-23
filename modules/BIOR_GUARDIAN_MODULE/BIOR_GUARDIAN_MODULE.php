<?php
	$MODULE_NAME = "BIOR_GUARDIAN_MODULE";

	//Bio Regrowth module
	bot::event("leavePriv", "$MODULE_NAME/bior_left_chat.php", "bior", "Remove player who leaves chat from bior list if he was on it");
	bot::event("joinPriv", "$MODULE_NAME/bior_joined_chat.php", "bior", "Add player to bior list when he joins chat if he should be on it (Keep,Adv,Enf,Eng)");
	bot::event("2sec", "$MODULE_NAME/bior_check.php", "bior", "Timer check for bior list");
	
	bot::command("", "$MODULE_NAME/bior_order.php", "bior", "leader", "Show Bio Regrowth Order");
	bot::command("", "$MODULE_NAME/cast_bior.php", "b", "all", "Show Bio Regrowth Cast");
	
	bot::regGroup("bior", $MODULE_NAME, "Create a Bio Regrowth List", "bior", "b");
	
	bot::addsetting($MODULE_NAME, "bior_max", "Max Persons that are shown on BioR list", "edit", "10", "10;15;20;25;30", '0', "mod", "$MODULE_NAME/bior_help.txt");

	//Helpfiles
	bot::help($MODULE_NAME, "bior", "bior.txt", "all", "Bio Regrowth Macro and List");
	
	//Guardian module
	bot::event("leavePriv", "$MODULE_NAME/guardian_left_chat.php", "guard", "Remove player who leaves chat from guardian list if he was on it");
	bot::event("joinPriv", "$MODULE_NAME/guardian_joined_chat.php", "guard", "Add player to guardian list when he joins chat if he should be on it (Soldier)");
	bot::event("2sec", "$MODULE_NAME/guard_check.php", "guard", "Timer check for guardian list");
	
	bot::command("", "$MODULE_NAME/guard_order.php", "guard", "leader", "Show Guardian Order");
	bot::command("", "$MODULE_NAME/cast_guard.php", "g", "all", "Show Guardian Cast");
	
	bot::regGroup("guardian", $MODULE_NAME, "Create a Guardian List", "guard", "g");
	
	bot::addsetting($MODULE_NAME, "guard_max", "Max Persons that are shown on Guard list", "edit", "10", "10;15;20;25;30", '0', "mod", "$MODULE_NAME/guard_help.txt");

	//Helpfiles
	bot::help($MODULE_NAME, "guard", "guard.txt", "all", "Guardian Macro and List");
?>