<?php
	require_once 'functions.php';

	$MODULE_NAME = "ITEMS_MODULE";

	//Load items db
	bot::loadSQLFile($MODULE_NAME, "aodb");
	
    //Items Search
	bot::command("", "$MODULE_NAME/items.php", "items", "all", "Searches for an item in the Database");

	//Settings
    bot::addsetting('maxitems', 'Number of Items shown on the list', 'edit', '40', '30;40;50;60', "0", "mod", "$MODULE_NAME/aodb_maxitems_help.txt");
	bot::addsetting('itemdb_location', 'Where to search for items', 'edit', 'local', 'local;Xyphos.com', "0", "mod");

	//Help files
    bot::help("items", "$MODULE_NAME/items.txt", "guild", "How to search for an item.");
?>