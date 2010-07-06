<?php
	$MODULE_NAME = "POCKETBOSS_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Setup
	bot::loadSQLFile($MODULE_NAME, "pocketboss");

	//Pocketboss module
	bot::command("", "$MODULE_NAME/pocketboss.php", "pb", ALL, "Shows what symbs a PB drops");
	bot::command("", "$MODULE_NAME/pocketboss.php", "symb", ALL, "Shows what PB drops a symb");

	bot::regGroup("PB_SYMB", $MODULE_NAME, "PocketBoss List and Symb search", "symb", "pb");

	//Helpiles
    bot::help("pocketboss", "$MODULE_NAME/pocketboss.txt", ALL, "See what drops which Pocketboss");
?>