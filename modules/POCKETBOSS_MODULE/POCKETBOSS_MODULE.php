<?php
	$MODULE_NAME = "POCKETBOSS_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Setup
	$this->loadSQLFile($MODULE_NAME, "pocketboss");

	//Pocketboss module
	$this->command("", "$MODULE_NAME/pocketboss.php", "pb", ALL, "Shows what symbs a PB drops");
	$this->command("", "$MODULE_NAME/pocketboss.php", "symb", ALL, "Shows what PB drops a symb");

	$this->regGroup("PB_SYMB", $MODULE_NAME, "PocketBoss List and Symb search", "symb", "pb");

	//Helpiles
    $this->help("pocketboss", "$MODULE_NAME/pocketboss.txt", ALL, "See what drops which Pocketboss");
?>