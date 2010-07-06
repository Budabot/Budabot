<?php
	$MODULE_NAME = "NANO_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Search for Database Updates
	bot::loadSQLFile($MODULE_NAME, "nanos");

    //nano Search
	bot::command("", "$MODULE_NAME/nano.php", "nano", ALL, "Searches for a nano and tells you were to get it.");

	//Settings
    bot::addsetting('maxnano', 'Number of Nanos shown on the list', 'edit', '40', '30;40;50;60', "0", MODERATOR, "$MODULE_NAME/maxnano_help.txt");

	//Help files
    bot::help("nano", "$MODULE_NAME/nano.txt", ALL, "How to search for a nano."); 
?>