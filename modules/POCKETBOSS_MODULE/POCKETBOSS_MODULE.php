<?php
	$MODULE_NAME = "POCKETBOSS_MODULE";

	//Setup
	bot::loadSQLFile($MODULE_NAME, "pocketboss");

	//Pocketboss module
	bot::command("", "$MODULE_NAME/pb.php", "pb", "all", "Shows what symbs a PB drops");
	bot::command("", "$MODULE_NAME/pb.php", "symb", "all", "Shows what PB drops a symb");

	bot::regGroup("PB_SYMB", $MODULE_NAME, "PocketBoss List and Symb search", "symb", "pb");

	//Helpiles
    bot::help($MODULE_NAME, "pb", "pb.txt", "all", "See what drops which Pocketboss");
?>