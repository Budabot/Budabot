<?php
	require_once 'functions.php';

	$MODULE_NAME = "ITEMS_MODULE";

	//Load items db
	bot::loadSQLFile($MODULE_NAME, "aodb");
	
    //Items Search
	bot::command("", "$MODULE_NAME/items.php", "items", "all", "Searches for an item in the Database");
	
	bot::command("", "$MODULE_NAME/updateitems.php", "updateitems", "guild", "Download the latest version of the items db");

	//Settings
    Setting::add($MODULE_NAME, 'maxitems', 'Number of Items shown on the list', 'edit', '40', '30;40;50;60', "0", "mod", "$MODULE_NAME/aodb_maxitems_help.txt");
	Setting::add($MODULE_NAME, 'itemdb_location', 'Where to search for items', 'edit', 'local', 'local;Xyphos.com', "0", "mod");

	//Help files
    bot::help($MODULE_NAME, "items", "items.txt", "guild", "How to search for an item.");
?>