<?php
	$MODULE_NAME = "POCKETBOSS_MODULE";

	//Setup
	bot::loadSQLFile($MODULE_NAME, "pocketboss");

	//Pocketboss module
	bot::command("", "$MODULE_NAME/pb.php", "pb", "all", "Shows what symbs a PB drops");
	bot::command("", "$MODULE_NAME/pb.php", "symb", "all", "Shows what PB drops a symb");

	//Helpiles
    Help::register($MODULE_NAME, "pb", "pb.txt", "all", "See what drops which Pocketboss");
	Help::register($MODULE_NAME, "symb", "symb.txt", "all", "See symbs are available");
?>