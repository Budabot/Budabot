<?php
	$MODULE_NAME = "POCKETBOSS_MODULE";

	//Setup
	bot::loadSQLFile($MODULE_NAME, "pocketboss");

	//Pocketboss module
	bot::command("", "$MODULE_NAME/pocketboss.php", "pb", "all", "Shows what symbs a PB drops");
	bot::command("", "$MODULE_NAME/pocketboss.php", "symb", "all", "Shows what PB drops a symb");

	bot::regGroup("PB_SYMB", $MODULE_NAME, "PocketBoss List and Symb search", "symb", "pb");

	//Helpiles
    bot::help($MODULE_NAME, "pocketboss", "pocketboss.txt", "all", "See what drops which Pocketboss");
?>