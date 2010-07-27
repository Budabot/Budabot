<?php
	$MODULE_NAME = "NANO_MODULE";

	//Search for Database Updates
	bot::loadSQLFile($MODULE_NAME, "nanos");

    //nano Search
	bot::command("", "$MODULE_NAME/nano.php", "nano", "all", "Searches for a nano and tells you were to get it.");

	//Settings
    bot::addsetting('maxnano', 'Number of Nanos shown on the list', 'edit', '40', '30;40;50;60', "0", "mod", "$MODULE_NAME/maxnano_help.txt");

	//Helpfiles
    bot::help("nano", "$MODULE_NAME/nano.txt", "guild", "How to search for a nano.", "Nano Search Module"); 
?>